<?php

namespace App\Services;

use App\Models\Committee;
use App\Models\LetterTemplate;
use App\Models\Member;
use App\Models\OutgoingLetter;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * Mesin pembuat surat dari template .docx.
 *
 * Alur: data preset (kepanitiaan) + isian spesifik surat  ->  "payload"
 * (kumpulan token => teks)  ->  TemplateProcessor mengganti ${token} di .docx.
 *
 * Token yang dikenal template (nama input form = nama token):
 *   kota, tanggal_surat, nomor_urut, bulan_romawi, tahun,
 *   nama_kegiatan, tema, hari_tanggal, jam_mulai, tempat,
 *   nama_penerima, jabatan_penerima, nama_instansi, nama_paguyuban, angkatan,
 *   latar_belakang, tujuan_utama, topik, harapan_lain,
 *   {ketua_panitia|sekretaris_panitia|ketua_umum|sekretaris_umum}_{nama|nim}
 */
class LetterGenerator
{
    public const DEFAULT_CITY = 'Lhokseumawe';

    public const SIGNER_LABELS = [
        'ketua_panitia' => 'Ketua Panitia',
        'sekretaris_panitia' => 'Sekretaris Panitia',
        'ketua_umum' => 'Ketua Umum',
        'sekretaris_umum' => 'Sekretaris Umum',
    ];

    private const DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    private const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    private const ROMAN = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    /** Isian "data kegiatan" yang otomatis diambil dari preset kepanitiaan. */
    public static function eventFields(): array
    {
        return [
            ['key' => 'nama_kegiatan', 'label' => 'Nama Kegiatan', 'type' => 'text', 'required' => true],
            ['key' => 'tema', 'label' => 'Tema Kegiatan', 'type' => 'text', 'required' => true],
            ['key' => 'hari_tanggal', 'label' => 'Hari/Tanggal Pelaksanaan', 'type' => 'text', 'required' => true,
                'help' => 'Contoh: Sabtu, 14 November 2026. Boleh diketik manual untuk kegiatan beberapa hari.'],
            ['key' => 'jam_mulai', 'label' => 'Jam Mulai', 'type' => 'time', 'required' => true],
            ['key' => 'tempat', 'label' => 'Tempat/Lokasi', 'type' => 'text', 'required' => true],
        ];
    }

    // ------------------------------------------------------------------
    // Format teks (murni PHP, tidak bergantung locale aplikasi)
    // ------------------------------------------------------------------

    public static function longDate(DateTimeInterface $date, bool $withDayName = false): string
    {
        $text = (int) $date->format('j') . ' ' . self::MONTHS[(int) $date->format('n')] . ' ' . $date->format('Y');

        return $withDayName ? self::DAYS[(int) $date->format('w')] . ', ' . $text : $text;
    }

    public static function romanMonth(DateTimeInterface $date): string
    {
        return self::ROMAN[(int) $date->format('n')];
    }

    /** "Sabtu, 14 November 2026" | "Jumat - Sabtu, 13 - 14 November 2026" | lintas bulan. */
    public static function eventDateLabel(?DateTimeInterface $start, ?DateTimeInterface $end): string
    {
        if (! $start) {
            return '';
        }
        if (! $end || $end->format('Y-m-d') <= $start->format('Y-m-d')) {
            return self::longDate($start, true);
        }

        if ($start->format('Y-n') === $end->format('Y-n')) {
            return self::DAYS[(int) $start->format('w')] . ' - ' . self::DAYS[(int) $end->format('w')] . ', '
                . (int) $start->format('j') . ' - ' . (int) $end->format('j') . ' '
                . self::MONTHS[(int) $start->format('n')] . ' ' . $start->format('Y');
        }

        return self::longDate($start, true) . ' - ' . self::longDate($end, true);
    }

    /**
     * Tanggal acara (bisa beberapa hari/terpisah) -> teks surat.
     * ["2026-10-17","2026-10-18"]            => "Sabtu - Minggu, 17 - 18 Oktober 2026"
     * ["2026-10-17","2026-10-19"]            => "Sabtu, 17 Oktober 2026 dan Senin, 19 Oktober 2026"
     */
    public static function eventDatesLabel(?array $dates): string
    {
        $dates = array_values(array_unique(array_filter((array) $dates)));
        sort($dates);
        if (! $dates) {
            return '';
        }

        // kelompokkan hari berurutan jadi satu rentang
        $runs = [];
        foreach ($dates as $iso) {
            $d = new DateTimeImmutable($iso);
            $last = count($runs) - 1;
            if ($last >= 0 && $runs[$last][1]->modify('+1 day')->format('Y-m-d') === $iso) {
                $runs[$last][1] = $d;
            } else {
                $runs[] = [$d, $d];
            }
        }

        $labels = array_map(fn ($r) => self::eventDateLabel($r[0], $r[1]), $runs);
        if (count($labels) === 1) {
            return $labels[0];
        }
        $lastLabel = array_pop($labels);

        return implode(', ', $labels) . ' dan ' . $lastLabel;
    }

    // ------------------------------------------------------------------
    // Nomor surat
    // ------------------------------------------------------------------

    /** Nomor urut berikutnya pada tahun tsb (satu urutan untuk surat A & B, sama dengan cara index mengurutkan). */
    public function nextNumber(DateTimeInterface $date): int
    {
        $max = OutgoingLetter::whereYear('date', $date->format('Y'))
            ->pluck('reference_number')
            ->map(fn ($ref) => (int) $ref)
            ->max();

        return ((int) $max) + 1;
    }

    public static function pad(int $no): string
    {
        return str_pad((string) $no, 3, '0', STR_PAD_LEFT);
    }

    /** Nomor yang disimpan di register surat keluar (tanpa spasi). */
    public function referenceNumber(LetterTemplate $template, int $no, DateTimeInterface $date): string
    {
        return strtr($template->number_pattern, [
            '{no}' => self::pad($no),
            '{tipe}' => $template->letter_type,
            '{romawi}' => self::romanMonth($date),
            '{tahun}' => $date->format('Y'),
        ]);
    }

    // ------------------------------------------------------------------
    // Isian form
    // ------------------------------------------------------------------

    /** Isian khusus template (penerima, angkatan, dst.). */
    public function templateFields(LetterTemplate $template): array
    {
        return $template->fields ?? [];
    }

    /** Semua isian bertipe teks yang boleh diisi pengguna (tanpa penandatangan). */
    public function allFields(LetterTemplate $template): array
    {
        return $template->isCommitteeBased()
            ? array_merge($this->templateFields($template), self::eventFields())
            : $this->templateFields($template);
    }

    /** Nilai awal form: dari preset kepanitiaan + penandatangan default. */
    public function defaults(LetterTemplate $template, ?Committee $committee): array
    {
        $today = new DateTimeImmutable('now');

        $defaults = [
            'kota' => self::DEFAULT_CITY,
            'tanggal_surat' => $today->format('Y-m-d'),
            'nomor_urut' => $this->nextNumber($today),
        ];

        if ($committee) {
            $defaults += [
                'nama_kegiatan' => $committee->name,
                'tema' => $committee->theme ?? '',
                'hari_tanggal' => self::eventDatesLabel($committee->event_dates),
                'jam_mulai' => $committee->start_time ? substr((string) $committee->start_time, 0, 5) : '',
                'tempat' => $committee->venue ?? '',
            ];
        }

        foreach ($template->signers as $role) {
            $member = $this->findSigner($committee, $role);
            $defaults[$role . '_nama'] = $member?->name ?? '';
            $defaults[$role . '_nim'] = $member?->student_id ?? '';
        }

        return $defaults;
    }

    private function findSigner(?Committee $committee, string $role): ?Member
    {
        return match ($role) {
            'ketua_panitia' => $this->committeeRole($committee, 'Ketua Panitia'),
            'sekretaris_panitia' => $this->committeeRole($committee, 'Sekretaris Panitia'),
            'ketua_umum' => $this->activeOfficer('Ketua Umum'),
            'sekretaris_umum' => $this->activeOfficer('Sekretaris Umum'),
            default => null,
        };
    }

    private function committeeRole(?Committee $committee, string $positionCategory): ?Member
    {
        if (! $committee) {
            return null;
        }

        return $committee->committeeMembers()
            ->where('position_category', $positionCategory)
            ->with('member')
            ->first()?->member;
    }

    private function activeOfficer(string $position): ?Member
    {
        return Member::where('position', $position)->where('status', 'Aktif')->first();
    }

    public function rules(LetterTemplate $template): array
    {
        $rules = [
            'kota' => ['required', 'string', 'max:100'],
            'tanggal_surat' => ['required', 'date'],
            'nomor_urut' => ['required', 'integer', 'min:1', 'max:9999'],
        ];

        foreach ($this->allFields($template) as $field) {
            // Tiap aturan = satu elemen array (jangan digabung "a|b" di dalam array).
            $rules[$field['key']] = array_merge(
                [($field['required'] ?? true) ? 'required' : 'nullable'],
                match ($field['type'] ?? 'text') {
                    'date' => ['date'],
                    'time' => ['date_format:H:i'],
                    'textarea' => ['string', 'max:1000'],
                    default => ['string', 'max:255'],
                }
            );
        }

        foreach ($template->signers as $role) {
            $rules[$role . '_nama'] = ['required', 'string', 'max:150'];
            $rules[$role . '_nim'] = ['required', 'string', 'max:50'];
        }

        return $rules;
    }

    /** Ubah hasil validasi menjadi payload token => teks siap tempel ke template. */
    public function buildPayload(LetterTemplate $template, array $validated): array
    {
        $date = new DateTimeImmutable($validated['tanggal_surat']);

        $payload = [
            'kota' => $this->clean($validated['kota']),
            'tanggal_surat' => self::longDate($date),
            'nomor_urut' => self::pad((int) $validated['nomor_urut']),
            'bulan_romawi' => self::romanMonth($date),
            'tahun' => $date->format('Y'),
        ];

        foreach ($this->allFields($template) as $field) {
            $value = $this->clean($validated[$field['key']] ?? '');

            $payload[$field['key']] = match ($field['type'] ?? 'text') {
                'date' => $value === '' ? '' : self::longDate(new DateTimeImmutable($value), true),
                'time' => str_replace(':', '.', $value),
                default => $value,
            };
        }

        foreach ($template->signers as $role) {
            $payload[$role . '_nama'] = $this->clean($validated[$role . '_nama']);
            $payload[$role . '_nim'] = $this->clean($validated[$role . '_nim']);
        }

        return $payload;
    }

    /** Teks kolom "tujuan" di register surat keluar. */
    public function destination(LetterTemplate $template, array $payload): string
    {
        $replace = [];
        foreach ($payload as $key => $value) {
            $replace['{' . $key . '}'] = $value;
        }

        return Str::limit(strtr($template->destination_pattern, $replace), 255, '');
    }

    private function clean(mixed $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', (string) $value));
    }

    // ------------------------------------------------------------------
    // Render .docx
    // ------------------------------------------------------------------

    /** Isi template, simpan ke file sementara, kembalikan path-nya (hapus setelah dikirim). */
    public function render(LetterTemplate $template, array $payload): string
    {
        // Wajib: tanpa ini karakter seperti & atau < di isian akan merusak XML .docx.
        Settings::setOutputEscapingEnabled(true);

        $processor = new TemplateProcessor($template->path());

        foreach ($processor->getVariables() as $variable) {
            $processor->setValue($variable, $payload[$variable] ?? '');
        }

        $base = tempnam(sys_get_temp_dir(), 'surat_');
        $target = $base . '.docx';
        $processor->saveAs($target);
        @unlink($base);

        return $target;
    }

    /**
     * PDF dibuat dengan mengonversi .docx hasil render memakai LibreOffice (headless),
     * jadi tampilannya sama persis dengan file Word. Butuh LibreOffice di server.
     */
    public function pdfAvailable(): bool
    {
        return Cache::remember('letter_generator.pdf_available', 3600, function () {
            try {
                $process = new Process([$this->libreOfficeBinary(), '--version'], null, $this->libreOfficeEnv());
                $process->setTimeout(30);
                $process->run();

                return $process->isSuccessful();
            } catch (\Throwable $e) {
                return false;
            }
        });
    }

    /** Render lalu konversi ke PDF; kembalikan path file PDF sementara. */
    public function renderPdf(LetterTemplate $template, array $payload): string
    {
        $docx = $this->render($template, $payload);
        $workDir = sys_get_temp_dir() . '/surat_pdf_' . Str::random(10);
        File::makeDirectory($workDir, 0775, true);

        try {
            $process = new Process([
                $this->libreOfficeBinary(),
                '-env:UserInstallation=file://' . $workDir . '/profile', // profil terpisah per proses -> aman dijalankan bersamaan
                '--headless', '--norestore', '--nolockcheck',
                '--convert-to', 'pdf',
                '--outdir', $workDir,
                $docx,
            ], null, $this->libreOfficeEnv());
            $process->setTimeout(120);
            $process->run();

            $pdf = $workDir . '/' . pathinfo($docx, PATHINFO_FILENAME) . '.pdf';

            if (! $process->isSuccessful() || ! is_file($pdf)) {
                throw new RuntimeException('Konversi PDF gagal: ' . trim($process->getErrorOutput() ?: $process->getOutput()));
            }

            $base = tempnam(sys_get_temp_dir(), 'surat_');
            $target = $base . '.pdf';
            File::move($pdf, $target);
            @unlink($base);

            return $target;
        } finally {
            @unlink($docx);
            File::deleteDirectory($workDir);
        }
    }

    private function libreOfficeBinary(): string
    {
        return (string) config('services.libreoffice.binary', 'soffice');
    }

    private function libreOfficeEnv(): array
    {
        // LibreOffice menulis konfigurasi ke $HOME; di server HOME sering tidak bisa ditulis.
        return ['HOME' => sys_get_temp_dir()];
    }

    public function downloadName(LetterTemplate $template, OutgoingLetter $letter, string $extension = 'docx'): string
    {
        $subject = $letter->payload['nama_kegiatan'] ?? $letter->destination;

        return sprintf(
            '%s_%s_%s_%s.%s',
            (string) $letter->date,
            Str::slug($template->name),
            Str::slug(Str::limit((string) $subject, 30, '')),
            Str::slug(explode('/', $letter->reference_number)[0]),
            $extension
        );
    }
}
