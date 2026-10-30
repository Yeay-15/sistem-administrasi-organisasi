<?php

namespace App\Support;

/**
 * Menentukan nasib tiap baris daftar panitia (tanpa menyentuh database):
 *   use    = pakai anggota yang sudah ada (exact / similar / dari file alias)
 *   create = orang baru -> dibuat sebagai Non-Pengurus
 *   skip   = ragu (ambigu / hanya 1 kata) -> tidak dimasukkan, minta alias
 */
class PanitiaImportPlanner
{
    /** @var array<string, string> norm nama -> nama tampilan orang baru */
    private array $newPeople = [];

    /** @param array<string, string> $aliases nama di daftar (dinormalisasi) => nama lengkap / NIM */
    public function __construct(
        private MemberNameMatcher $matcher,
        private array $aliases = [],
        private bool $createMissing = true,
    ) {
    }

    /** @return array<string, string> */
    public function newPeople(): array
    {
        return $this->newPeople;
    }

    /** Kunci pembanding bidang: huruf kecil, tanpa awalan "Sie/Seksi/Bidang". */
    public static function fieldKey(string $name): string
    {
        return trim(preg_replace('/^(sie|seksi|bidang)\s+/', '', MemberNameMatcher::normalize($name)));
    }

    /** Apakah mayoritas nama bidang yang sudah ada memakai awalan "Sie"? */
    public static function usesSiePrefix(array $existingNames): bool
    {
        if (! $existingNames) {
            return false;
        }
        $with = count(array_filter($existingNames, fn ($n) => (bool) preg_match('/^\s*sie\s+/i', $n)));

        return $with * 2 > count($existingNames);
    }

    /** Nama bidang baru, mengikuti gaya penamaan yang sudah ada ("Acara" atau "Sie Acara"). */
    public static function newFieldName(string $title, bool $withSie): string
    {
        $title = preg_replace('/^(sie|seksi|bidang)\s+/i', '', trim($title));
        $title = str_replace(' Dan ', ' dan ', mb_convert_case($title, MB_CASE_TITLE));

        return ($withSie ? 'Sie ' : '') . $title;
    }

    public static function displayName(string $name): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', $name));
        $words = array_map(function (string $w) {
            // Huruf pertama dibesarkan; sisanya dibiarkan (agar "Az-Dzikri", "AL" tidak rusak).
            return mb_strtoupper(mb_substr($w, 0, 1)) . mb_substr($w, 1);
        }, explode(' ', $name));

        return implode(' ', $words);
    }

    /**
     * @param array{position: string, field: ?string, name: string, line: int} $entry
     * @return array{action: string, status: string, member_id: ?int, member_name: ?string, new_key: ?string, note: string}
     */
    public function plan(array $entry): array
    {
        $norm = MemberNameMatcher::normalize($entry['name']);

        // 1. Alias dari file menang atas segalanya.
        if (isset($this->aliases[$norm])) {
            $found = $this->matcher->find($this->aliases[$norm]);
            if ($found) {
                return $this->use($found, 'alias', 'dari file alias');
            }

            return $this->skip('alias-gagal', "alias \"{$this->aliases[$norm]}\" tidak ditemukan di data anggota");
        }

        // 2. Orang baru yang sudah dibuat pada baris sebelumnya (mis. muncul di dua kepanitiaan).
        if (isset($this->newPeople[$norm])) {
            return ['action' => 'create', 'status' => 'new', 'member_id' => null, 'member_name' => $this->newPeople[$norm], 'new_key' => $norm, 'note' => 'orang baru (sudah dijadwalkan)'];
        }

        $r = $this->matcher->match($entry['name']);

        return match ($r['status']) {
            'exact' => $this->use($r['candidates'][0], 'exact', ''),
            'similar' => $this->use($r['candidates'][0], 'similar', 'nama mirip, cek'),
            'review' => $this->skip('review', 'ragu: ' . implode(' | ', array_map(fn ($c) => $c['name'] . ($c['student_id'] ? " ({$c['student_id']})" : ''), $r['candidates']))),
            default => $this->createMissing ? $this->create($entry['name'], $norm) : $this->skip('none', 'tidak ditemukan'),
        };
    }

    private function use(array $m, string $status, string $note): array
    {
        return ['action' => 'use', 'status' => $status, 'member_id' => $m['id'], 'member_name' => $m['name'], 'new_key' => null, 'note' => $note];
    }

    private function skip(string $status, string $note): array
    {
        return ['action' => 'skip', 'status' => $status, 'member_id' => null, 'member_name' => null, 'new_key' => null, 'note' => $note];
    }

    private function create(string $name, string $norm): array
    {
        $display = self::displayName($name);
        $this->newPeople[$norm] = $display;

        return ['action' => 'create', 'status' => 'new', 'member_id' => null, 'member_name' => $display, 'new_key' => $norm, 'note' => 'tidak ada di data -> dibuat sebagai Non-Pengurus'];
    }
}
