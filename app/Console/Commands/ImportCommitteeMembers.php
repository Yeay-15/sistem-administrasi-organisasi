<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Committee;
use App\Models\CommitteeField;
use App\Models\CommitteeMember;
use App\Models\Member;
use App\Support\MemberNameMatcher;
use App\Support\PanitiaImportPlanner;
use App\Support\PanitiaListParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCommitteeMembers extends Command
{
    protected $signature = 'panitia:import
        {file=database/data/panitia-2026.txt : Berkas daftar panitia}
        {--map=* : Petakan judul daftar ke nama kepanitiaan, mis. --map="Makrab=Malam Keakraban 2026"}
        {--aliases= : Berkas alias untuk nama yang ragu/tidak ketemu (lihat database/data/panitia-aliases.txt)}
        {--no-create : Jangan buat anggota Non-Pengurus baru untuk nama yang tidak ada di data}
        {--all : Tampilkan semua baris, bukan hanya yang perlu dicek}
        {--dry-run : Hanya tampilkan rencana, tidak menyimpan apa pun}';

    protected $description = 'Impor susunan panitia dari berkas teks (cocokkan nama ke anggota, orang baru jadi Non-Pengurus).';

    /** Judul di daftar -> kata kunci nama kepanitiaan (dipakai bila --map tidak diberikan). */
    private const TITLE_HINTS = [
        'makrab' => 'keakraban',
    ];

    public function handle(): int
    {
        $path = base_path($this->argument('file'));
        if (! is_file($path)) {
            $path = $this->argument('file');
        }
        if (! is_file($path)) {
            $this->error('Berkas tidak ditemukan: ' . $this->argument('file'));

            return self::FAILURE;
        }

        try {
            $sections = PanitiaListParser::parse((string) file_get_contents($path));
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
        if (! $sections) {
            $this->error('Tidak ada kepanitiaan yang terbaca dari berkas.');

            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');

        // Kepanitiaan tujuan untuk tiap judul
        $targets = [];
        foreach ($sections as $s) {
            $committee = $this->resolveCommittee($s['title']);
            if (! $committee) {
                return self::FAILURE;
            }
            $targets[$s['title']] = $committee;
        }

        $members = Member::query()->get(['id', 'name', 'student_id', 'membership_type']);
        $typeById = $members->pluck('membership_type', 'id')->all();
        $matcher = new MemberNameMatcher($members->map(fn ($m) => ['id' => $m->id, 'name' => $m->name, 'student_id' => $m->student_id])->all());
        $planner = new PanitiaImportPlanner($matcher, $this->loadAliases(), ! $this->option('no-create'));

        $plans = []; // per judul: list of [entry, plan]
        $stats = ['use' => 0, 'create' => 0, 'skip' => 0];
        foreach ($sections as $s) {
            $this->newLine();
            $this->info("== {$s['title']}  ->  kepanitiaan \"{$targets[$s['title']]->name}\" (" . count($s['entries']) . ' orang)');

            $rows = [];
            $count = [];
            foreach ($s['entries'] as $entry) {
                $plan = $planner->plan($entry);
                $plans[$s['title']][] = [$entry, $plan];
                $stats[$plan['action']]++;
                $label = $entry['field'] ?? $entry['position'];
                $count[$label] = ($count[$label] ?? 0) + 1;

                $needsCheck = ! in_array($plan['status'], ['exact'], true);
                if ($this->option('all') || $needsCheck) {
                    $type = $plan['member_id'] ? ($typeById[$plan['member_id']] ?? '') : '';
                    $rows[] = [
                        $entry['field'] ? "{$entry['field']} (Anggota)" : $entry['position'],
                        $entry['name'],
                        $plan['member_name'] ? $plan['member_name'] . ($type ? " [{$type}]" : '') : '-',
                        $this->statusLabel($plan['status']) . ($plan['note'] ? ': ' . $plan['note'] : ''),
                    ];
                }
            }
            if ($rows) {
                $this->table(['Peran / Bidang', 'Nama di daftar', 'Dicocokkan ke', 'Hasil'], $rows);
            } else {
                $this->line('Semua nama cocok persis.');
            }
            $this->line('Ringkas: ' . collect($count)->map(fn ($n, $k) => "{$k} {$n}")->implode(' | '));
        }

        // Bidang: yang sudah ada dipakai, sisanya dibuat dengan gaya penamaan yang sama
        $existingNames = CommitteeField::pluck('name')->all();
        $existingKeys = array_map([PanitiaImportPlanner::class, 'fieldKey'], $existingNames);
        $withSie = PanitiaImportPlanner::usesSiePrefix($existingNames);
        $used = [];
        $created = [];
        foreach ($sections as $s) {
            foreach ($s['entries'] as $e) {
                if (! $e['field']) {
                    continue;
                }
                $key = PanitiaImportPlanner::fieldKey($e['field']);
                if (in_array($key, $existingKeys, true)) {
                    $used[$key] = $e['field'];
                } else {
                    $created[$key] = PanitiaImportPlanner::newFieldName($e['field'], $withSie);
                }
            }
        }
        $this->newLine();
        $this->line('Bidang yang sudah ada & dipakai: ' . ($used ? implode(', ', $used) : '-'));
        $this->line('Bidang baru yang akan dibuat: ' . ($created ? implode(', ', $created) : '-'));

        $newCount = count($planner->newPeople());
        $this->newLine();
        $this->info(sprintf(
            'Total: %d dipakai dari data anggota, %d baris untuk %d orang baru (Non-Pengurus), %d dilewati.',
            $stats['use'], $stats['create'], $newCount, $stats['skip']
        ));
        if ($newCount) {
            $this->line('Orang baru: ' . implode(', ', $planner->newPeople()));
        }
        if ($stats['skip']) {
            $this->warn('Yang dilewati perlu alias: isi database/data/panitia-aliases.txt lalu jalankan ulang dengan --aliases=...');
        }

        if ($dry) {
            $this->comment('--dry-run: tidak ada yang disimpan.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($planner, $plans, $targets) {
            // 1. Orang baru -> anggota Non-Pengurus
            $createdIds = [];
            foreach ($planner->newPeople() as $key => $display) {
                $createdIds[$key] = Member::create([
                    'name' => $display,
                    'membership_type' => 'Non-Pengurus',
                    'status' => 'Aktif',
                ])->id;
            }

            // 2. Bidang (pakai yang ada, buat bila belum)
            $fieldCache = $this->existingFields();

            $added = 0;
            foreach ($plans as $title => $list) {
                $committee = $targets[$title];
                foreach ($list as [$entry, $plan]) {
                    if ($plan['action'] === 'skip') {
                        continue;
                    }
                    $memberId = $plan['action'] === 'create' ? $createdIds[$plan['new_key']] : $plan['member_id'];

                    $fieldId = null;
                    if ($entry['field']) {
                        $fieldId = $this->fieldId($entry['field'], $fieldCache);
                    }

                    $cm = CommitteeMember::firstOrCreate([
                        'committee_id' => $committee->id,
                        'member_id' => $memberId,
                        'position_category' => $entry['position'],
                        'committee_field_id' => $fieldId,
                    ]);
                    $added += $cm->wasRecentlyCreated ? 1 : 0;
                }
            }

            AuditLog::record('Impor Anggota Kepanitiaan', "Impor panitia dari berkas: {$added} baris baru, " . count($createdIds) . ' anggota Non-Pengurus baru.');
            $this->info("Tersimpan: {$added} susunan panitia baru, " . count($createdIds) . ' anggota Non-Pengurus baru.');
        });

        return self::SUCCESS;
    }

    private function statusLabel(string $status): string
    {
        return [
            'exact' => 'cocok',
            'similar' => 'MIRIP',
            'alias' => 'alias',
            'new' => 'BARU',
            'review' => 'RAGU (dilewati)',
            'none' => 'tidak ketemu (dilewati)',
            'alias-gagal' => 'alias gagal (dilewati)',
        ][$status] ?? $status;
    }

    private function resolveCommittee(string $title): ?Committee
    {
        // --map="Makrab=Malam Keakraban 2026"
        foreach ((array) $this->option('map') as $pair) {
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, '');
            if (MemberNameMatcher::normalize($k) === MemberNameMatcher::normalize($title)) {
                $c = Committee::where('name', trim($v))->first();
                if (! $c) {
                    $this->error("Kepanitiaan \"{$v}\" (dari --map) tidak ada.");
                }

                return $c;
            }
        }

        $key = MemberNameMatcher::normalize($title);
        $needles = array_filter([$title, self::TITLE_HINTS[$key] ?? null]);
        foreach ($needles as $needle) {
            $found = Committee::where('name', 'like', '%' . $needle . '%')->get();
            if ($found->count() === 1) {
                return $found->first();
            }
            if ($found->count() > 1) {
                $this->error("Judul \"{$title}\" cocok dengan beberapa kepanitiaan: " . $found->pluck('name')->implode(', ') . '. Pakai --map="' . $title . '=NAMA PERSIS".');

                return null;
            }
        }

        $this->error("Kepanitiaan untuk judul \"{$title}\" tidak ditemukan. Yang ada: " . Committee::pluck('name')->implode(', ') . '. Pakai --map="' . $title . '=NAMA PERSIS".');

        return null;
    }

    /** @return array<string, string> norm nama => nama lengkap/NIM */
    private function loadAliases(): array
    {
        $file = $this->option('aliases');
        if (! $file) {
            return [];
        }
        $path = is_file(base_path($file)) ? base_path($file) : $file;
        if (! is_file($path)) {
            $this->warn("Berkas alias tidak ditemukan: {$file}");

            return [];
        }

        $aliases = [];
        foreach (preg_split('/\R/u', (string) file_get_contents($path)) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }
            [$from, $to] = array_map('trim', explode('=', $line, 2));
            if ($from !== '' && $to !== '') {
                $aliases[MemberNameMatcher::normalize($from)] = $to;
            }
        }

        return $aliases;
    }

    /** @return array<string, int> kunci bidang (tanpa awalan Sie) => id */
    private function existingFields(): array
    {
        $map = [];
        foreach (CommitteeField::all() as $f) {
            $map[PanitiaImportPlanner::fieldKey($f->name)] = $f->id;
        }

        return $map;
    }

    private function fieldId(string $title, array &$cache): int
    {
        $key = PanitiaImportPlanner::fieldKey($title);
        if (! isset($cache[$key])) {
            $withSie = PanitiaImportPlanner::usesSiePrefix(CommitteeField::pluck('name')->all());
            $cache[$key] = CommitteeField::create(['name' => ($name = PanitiaImportPlanner::newFieldName($title, $withSie)), 'sort_order' => CommitteeField::presetOrder($name)])->id;
        }

        return $cache[$key];
    }
}
