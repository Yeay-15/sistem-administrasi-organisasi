<?php

namespace App\Imports;

use App\Models\InventoryItem;
use App\Support\InventoryRowParser;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * Impor data inventaris dari file Excel (.xlsx / .xls).
 *
 * Alurnya dua tahap supaya aman:
 *  1. read()  -> membaca & memeriksa SEMUA baris, belum menulis apa pun ke DB.
 *  2. save()  -> hanya dipanggil bila tidak ada error; ditulis dalam satu
 *                transaksi (semua masuk atau tidak sama sekali).
 *
 * File dibaca langsung dengan PhpSpreadsheet (bukan lewat concern import
 * Laravel Excel) agar perilakunya tetap sama walau versi paket Excel berganti.
 */
class InventoryItemsImport
{
    public const MAX_ROWS = 1000;

    /** Batas pemindaian baris tersimpan (termasuk yang kosong) demi keamanan memori. */
    private const SCAN_LIMIT = 5000;

    /** Baris valid, siap disimpan. */
    public array $rows = [];

    /** @var array<int, array{line: int, messages: string[]}> */
    public array $errors = [];

    /** Pesan error yang membuat seluruh file tidak bisa diproses. */
    public ?string $fatal = null;

    public function __construct(private string $path)
    {
    }

    public function read(): self
    {
        try {
            // Hanya izinkan format Excel; jangan biarkan PhpSpreadsheet menebak
            // format lain (HTML, XML, dst) dari file yang diunggah.
            $reader = IOFactory::createReaderForFile($this->path, ['Xlsx', 'Xls']);
            $reader->setReadDataOnly(true);
            $sheet = $reader->load($this->path)->getSheet(0);

            // Pakai baris/kolom yang benar-benar tersimpan di file, bukan seluruh
            // lembar, supaya file dengan sangat banyak baris kosong berformat
            // tidak menghabiskan memori.
            $lastRow = $sheet->getHighestDataRow();

            if ($lastRow > self::SCAN_LIMIT) {
                $this->fatal = 'File terlalu besar (lebih dari ' . self::SCAN_LIMIT . ' baris, termasuk baris kosong berformat). Salin datanya ke template yang baru.';

                return $this;
            }

            $matrix = $sheet->rangeToArray('A1:' . $sheet->getHighestDataColumn() . $lastRow, null, true, false, false);

            // Buang baris kosong di bagian bawah (mis. baris yang hanya berformat).
            while ($matrix !== [] && $this->isBlankRow(end($matrix))) {
                array_pop($matrix);
            }
        } catch (Throwable $e) {
            report($e);
            $this->fatal = 'File tidak bisa dibaca. Pastikan file berformat .xlsx/.xls dan tidak rusak.';

            return $this;
        }

        if (count($matrix) < 2) {
            $this->fatal = 'File tidak berisi data. Isi data mulai dari baris ke-2, di bawah judul kolom.';

            return $this;
        }

        $headings = array_map(fn ($heading) => InventoryRowParser::slug((string) $heading), array_shift($matrix));

        if (! InventoryRowParser::hasRequiredColumns($headings)) {
            $this->fatal = 'Kolom "Nama Barang" dan "Jumlah" tidak ditemukan di baris pertama. Gunakan template yang disediakan dan jangan ubah judul kolomnya.';

            return $this;
        }

        if (count($matrix) > self::MAX_ROWS) {
            $this->fatal = 'Maksimal ' . self::MAX_ROWS . ' baris per impor. Bagi file menjadi beberapa bagian.';

            return $this;
        }

        $parser = new InventoryRowParser();

        foreach ($matrix as $index => $cells) {
            $row = [];
            foreach ($headings as $column => $key) {
                if ($key !== '') {
                    $row[$key] = $cells[$column] ?? null;
                }
            }

            $result = $parser->parse($row);

            if ($result['errors'] !== []) {
                // +2: baris 1 adalah judul kolom, dan indeks array mulai dari 0.
                $this->errors[] = ['line' => $index + 2, 'messages' => $result['errors']];
            } elseif ($result['data'] !== null) {
                $this->rows[] = $result['data'];
            }
        }

        return $this;
    }

    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Simpan semua baris valid. Barang dianggap SUDAH ADA bila nama + tempat
     * sama (huruf besar/kecil diabaikan).
     *
     * @param  string  $onDuplicate  'skip' = lewati, 'update' = ganti dengan isi file
     * @return array{created: int, updated: int, skipped: int}
     */
    public function save(string $onDuplicate): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        DB::transaction(function () use ($onDuplicate, &$result) {
            foreach ($this->rows as $data) {
                $query = InventoryItem::whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])]);

                if ($data['location'] === null) {
                    $query->whereNull('location');
                } else {
                    $query->whereRaw('LOWER(location) = ?', [mb_strtolower($data['location'])]);
                }

                $existing = $query->first();

                if (! $existing) {
                    // Barang baru tanpa tanggal di file -> otomatis hari ini.
                    $data['added_at'] ??= today()->toDateString();
                    InventoryItem::create($data);
                    $result['created']++;

                    continue;
                }

                if ($onDuplicate === 'update') {
                    // Tanggal ditambahkan hanya diganti bila file mengisinya.
                    if ($data['added_at'] === null) {
                        unset($data['added_at']);
                    }
                    $existing->update($data);
                    $result['updated']++;
                } else {
                    $result['skipped']++;
                }
            }
        });

        return $result;
    }
}
