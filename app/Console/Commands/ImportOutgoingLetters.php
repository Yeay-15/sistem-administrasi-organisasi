<?php

namespace App\Console\Commands;

use App\Models\OutgoingLetter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Impor massal Surat Keluar dari folder berisi PDF.
 *
 *   php artisan surat:import "D:\Download\drive-download-20261026T170327Z-1-001" --dry-run
 *   php artisan surat:import "D:\Download\drive-download-20261026T170327Z-1-001"
 */
class ImportOutgoingLetters extends Command
{
    protected $signature = 'surat:import
        {folder : Folder berisi file PDF}
        {--dry-run : Hanya tampilkan hasil baca, tidak menyimpan apa pun}
        {--exclude=STAMBUK : Lewati file yang namanya mengandung kata ini (pisahkan dengan koma)}
        {--status=Terkirim : Draft, Terkirim, atau Dibatalkan}
        {--signatory=Ketua Panitia, Sekretaris Panitia, Ketua Umum : Penandatangan untuk semua surat}
        {--only= : Hanya proses file yang namanya mengandung kata ini (mis. SIBOLGA)}';

    protected $description = 'Impor surat keluar (PDF) dari sebuah folder ke tabel outgoing_letters';

    private const MONTHS = [
        'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 'mei' => 5, 'juni' => 6,
        'juli' => 7, 'agustus' => 8, 'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
    ];

    public function handle(): int
    {
        $folder = rtrim($this->argument('folder'), "\\/");
        if (! is_dir($folder)) {
            $this->error("Folder tidak ditemukan: {$folder}");

            return self::FAILURE;
        }

        $status = ucfirst(strtolower($this->option('status')));
        if (! in_array($status, ['Draft', 'Terkirim', 'Dibatalkan'], true)) {
            $this->error('--status harus Draft, Terkirim, atau Dibatalkan.');

            return self::FAILURE;
        }

        $excludes = array_filter(array_map('trim', explode(',', (string) $this->option('exclude'))));
        $only = trim((string) $this->option('only'));
        $dry = (bool) $this->option('dry-run');

        $files = collect(File::files($folder))
            ->filter(fn ($f) => strtolower($f->getExtension()) === 'pdf')
            ->sortBy(fn ($f) => $f->getFilename(), SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $rows = [];
        $saved = 0;
        $seen = []; // nomor yang sudah diproses di run ini (deteksi nomor ganda antar file)

        foreach ($files as $file) {
            $name = $file->getFilename();

            if ($only !== '' && stripos($name, $only) === false) {
                continue;
            }

            foreach ($excludes as $word) {
                if (stripos($name, $word) !== false) {
                    $rows[] = [$name, '-', '-', '-', 'DILEWATI (exclude)'];
                    continue 2;
                }
            }

            $text = $this->extractText($file->getPathname());
            $data = $this->parse($text, $name);

            $problems = [];
            foreach (['reference_number' => 'nomor', 'date' => 'tanggal', 'type' => 'jenis (A/B)', 'destination' => 'tujuan'] as $key => $label) {
                if (! $data[$key]) {
                    $problems[] = "{$label} tidak terbaca";
                }
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                $problems[] = 'file >5MB';
            }
            if ($problems) {
                $rows[] = [$name, $data['reference_number'] ?: '?', $data['date'] ?: '?', $data['destination'] ?: '?', 'GAGAL: '.implode('; ', $problems)];
                continue;
            }

            $no = $data['reference_number'];

            if (isset($seen[$no])) {
                $rows[] = [$name, $no, $data['date'], $data['destination'], "NOMOR GANDA dgn \"{$seen[$no]}\" - cek isi PDF, dilewati"];
                continue;
            }
            $seen[$no] = $name;

            if (OutgoingLetter::where('reference_number', $no)->exists()) {
                $rows[] = [$name, $no, $data['date'], $data['destination'], 'SUDAH ADA di database (dilewati)'];
                continue;
            }

            if (! $dry) {
                $path = 'outgoing_letters/'.sprintf(
                    '%s_%s_%s_%s.pdf',
                    $data['date'],
                    Str::slug($no, '-'),
                    Str::slug($data['destination'], '-'),
                    Str::slug(Str::limit($data['subject'], 30, ''), '-')
                );
                Storage::disk('public')->put($path, File::get($file->getPathname()));

                OutgoingLetter::create([
                    'reference_number' => $no,
                    'date' => $data['date'],
                    'type' => $data['type'],
                    'subject' => $data['subject'],
                    'destination' => $data['destination'],
                    'signatory' => (string) $this->option('signatory'),
                    'status' => $status,
                    'file_path' => $path,
                ]);
                $saved++;
            }

            $rows[] = [$name, $no, $data['date'], $data['destination'], $dry ? 'SIAP (dry-run)' : 'TERSIMPAN'];
        }

        $this->table(['File', 'Nomor', 'Tanggal', 'Tujuan', 'Hasil'], $rows);
        $this->info($dry ? 'Dry-run selesai, tidak ada yang disimpan.' : "Selesai. {$saved} surat disimpan.");

        return self::SUCCESS;
    }

    /** Baca teks PDF: pdftotext bila ada (paling rapi), kalau tidak smalot/pdfparser. */
    private function extractText(string $path): string
    {
        try {
            $p = new Process(['pdftotext', '-layout', $path, '-']);
            $p->run();
            if ($p->isSuccessful() && trim($p->getOutput()) !== '') {
                return $p->getOutput();
            }
        } catch (\Throwable $e) {
            // pdftotext tidak terpasang, lanjut ke parser PHP
        }

        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                return (new \Smalot\PdfParser\Parser())->parseFile($path)->getText();
            } catch (\Throwable $e) {
            }
        }

        return '';
    }

    /** Ratakan spasi/newline jadi satu spasi. */
    private function flat(string $s): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $s));
    }

    private function parse(string $text, string $filename): array
    {
        $text = str_replace("\xC2\xA0", ' ', $text);

        // Nomor: bisa terpotong 2 baris ("...KATIBERLHOKSEUMAWE- ACEH\n UTARA/X/2026").
        // Ambil semua sampai "Lampiran", buang SEMUA spasi -> format sama dengan input manual sebelumnya.
        $ref = null;
        if (preg_match('/Nomor\s*:\s*(.+?)\s*Lampiran/su', $text, $m)) {
            $ref = preg_replace('/\s+/u', '', $m[1]);
        }

        // Jenis surat dari nomor: 010/A/... -> A (Internal), 040/B/... -> B (Eksternal)
        $type = ($ref && preg_match('#^\d+/([AB])/#', $ref, $m)) ? $m[1] : null;

        // Tanggal: kemunculan pertama "14 Oktober 2026" (kop "Lhokseumawe, 14 Oktober 2026")
        $date = null;
        if (preg_match('/(\d{1,2})\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+(\d{4})/iu', $text, $m)) {
            $date = sprintf('%04d-%02d-%02d', $m[3], self::MONTHS[mb_strtolower($m[2])], $m[1]);
        }

        // Perihal: "Perihal : Undangan"
        $subject = 'Surat Undangan';
        if (preg_match('/Perihal\s*:\s*(.+?)\s*(?:Kepada\s+Yth|Yth\.)/isu', $text, $m)) {
            $subject = Str::limit($this->flat($m[1]), 255, '');
        }

        // Tujuan: teks antara "Yth." dan "di Tempat"
        //   "Kepada Yth.\nSTAMBUK 2020\ndi\nTempat"       -> STAMBUK 2020
        //   "Yth. Abang Faiz Fadilah\ndi\nTempat"          -> Abang Faiz Fadilah
        $destination = null;
        if (preg_match('/Yth\.?\s+(.+?)\s+di\s+Tempat/isu', $text, $m)) {
            $destination = Str::limit($this->flat($m[1]), 255, '');
        }
        if (! $destination) {
            // cadangan: dari nama file
            $base = preg_replace('/^\s*SURAT\s+UNDANGAN\s+/i', '', pathinfo($filename, PATHINFO_FILENAME));
            $base = trim(preg_replace('/\s*(fix+\s*\d*)\s*$/i', '', $base));
            $destination = $base !== '' ? Str::title(Str::lower($base)) : null;
        }

        return [
            'reference_number' => $ref ?: null,
            'type' => $type,
            'date' => $date,
            'subject' => $subject,
            'destination' => $destination,
        ];
    }
}
