<?php

namespace App\Support;

use DateTimeImmutable;

/**
 * Mengubah SATU baris Excel (sudah berupa array berkunci judul kolom) menjadi
 * data barang inventaris yang bersih + daftar pesan error berbahasa Indonesia.
 *
 * Sengaja tidak bergantung pada Laravel/Excel sama sekali (hanya PHP biasa)
 * supaya mudah diuji dan aturan format-nya ada di satu tempat.
 */
class InventoryRowParser
{
    /** Nama kolom (setelah di-slug) yang dikenali untuk tiap field. */
    private const ALIASES = [
        'name' => ['nama_barang', 'nama', 'barang'],
        'quantity' => ['jumlah', 'qty'],
        'unit' => ['satuan'],
        'location' => ['tempat', 'lokasi'],
        'added_at' => ['tanggal_ditambahkan', 'ditambahkan'],
        'expired_at' => ['tanggal_kedaluwarsa', 'kedaluwarsa', 'tanggal_expired', 'exp'],
        'notes' => ['keterangan', 'catatan'],
    ];

    /** "Nama Barang" -> "nama_barang" */
    public static function slug(string $heading): string
    {
        $heading = strtolower(trim($heading));

        return trim((string) preg_replace('/[^a-z0-9]+/', '_', $heading), '_');
    }

    /** File dianggap memakai format yang benar bila ada kolom nama DAN jumlah. */
    public static function hasRequiredColumns(array $slugs): bool
    {
        return array_intersect($slugs, self::ALIASES['name']) !== []
            && array_intersect($slugs, self::ALIASES['quantity']) !== [];
    }

    /**
     * @return array{data: ?array, errors: string[]} data = null untuk baris kosong.
     */
    public function parse(array $row): array
    {
        $values = [];
        foreach (self::ALIASES as $field => $aliases) {
            $values[$field] = null;
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $row) && $this->filled($row[$alias])) {
                    $values[$field] = $row[$alias];
                    break;
                }
            }
        }

        if (count(array_filter($values, fn ($value) => $value !== null)) === 0) {
            return ['data' => null, 'errors' => []];
        }

        $errors = [];

        // Nama
        $name = $this->text($values['name']);
        if ($name === null) {
            $errors[] = 'Nama barang wajib diisi.';
        } elseif (mb_strlen($name) > 255) {
            $errors[] = 'Nama barang maksimal 255 karakter.';
        }

        // Jumlah (boleh "7", "0,5", atau "1 gulung" -> 1 + satuan gulung)
        $quantity = null;
        $unitFromQuantity = null;
        $rawQuantity = $values['quantity'];
        if ($rawQuantity === null) {
            $errors[] = 'Jumlah wajib diisi.';
        } elseif (is_int($rawQuantity) || is_float($rawQuantity)) {
            $quantity = (float) $rawQuantity;
        } else {
            $text = trim((string) $rawQuantity);
            if (preg_match('/^(\d+(?:[.,]\d+)?|½)\s*(.*)$/u', $text, $match)) {
                $quantity = $match[1] === '½' ? 0.5 : (float) str_replace(',', '.', $match[1]);
                $rest = trim($match[2]);
                $unitFromQuantity = $rest !== '' ? $rest : null;
            } else {
                $errors[] = 'Jumlah "' . $text . '" tidak valid (harus berupa angka).';
            }
        }
        if ($quantity !== null) {
            if ($quantity < 0 || $quantity > 99999999) {
                $errors[] = 'Jumlah harus antara 0 dan 99.999.999.';
                $quantity = null;
            } else {
                $quantity = round($quantity, 2);
            }
        }

        // Satuan, Tempat, Keterangan
        $unit = $this->text($values['unit']) ?? $unitFromQuantity;
        if ($unit !== null && mb_strlen($unit) > 50) {
            $errors[] = 'Satuan maksimal 50 karakter.';
        }

        $location = $this->text($values['location']);
        if ($location !== null && mb_strlen($location) > 100) {
            $errors[] = 'Tempat maksimal 100 karakter.';
        }

        $notes = $this->text($values['notes']);
        if ($notes !== null && mb_strlen($notes) > 1000) {
            $errors[] = 'Keterangan maksimal 1000 karakter.';
        }

        // Tanggal
        $addedAt = $this->date($values['added_at'], 'Tanggal ditambahkan', $errors);
        $expiredAt = $this->date($values['expired_at'], 'Tanggal kedaluwarsa', $errors);

        if ($errors !== []) {
            return ['data' => null, 'errors' => $errors];
        }

        return [
            'data' => [
                'name' => $name,
                'quantity' => $quantity,
                'unit' => $unit,
                'location' => $location,
                'added_at' => $addedAt,
                'expired_at' => $expiredAt,
                'notes' => $notes,
            ],
            'errors' => [],
        ];
    }

    private function filled(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    private function text(mixed $value): ?string
    {
        return $this->filled($value) ? trim((string) $value) : null;
    }

    /**
     * Menerima: serial tanggal Excel, "2026-10-10", "10/10/2026" (dd/mm/yyyy),
     * dan "10/27" atau "10/2027" (bulan/tahun -> tanggal terakhir bulan itu).
     * Awalan "exp" dan tanda kurung diabaikan, mis. "(exp 12/28)".
     *
     * @param  string[]  $errors
     */
    private function date(mixed $raw, string $label, array &$errors): ?string
    {
        if (! $this->filled($raw)) {
            return null;
        }

        $invalid = $label . ' tidak dikenali. Gunakan format dd/mm/yyyy, contoh 31/12/2026.';

        $serial = null;
        if (is_int($raw) || is_float($raw)) {
            $serial = $raw;
        } elseif (preg_match('/^\d{5}(\.\d+)?$/', trim((string) $raw))) {
            $serial = (float) trim((string) $raw);
        }

        if ($serial !== null) {
            if ($serial < 20000 || $serial > 80000) {
                $errors[] = $invalid;

                return null;
            }

            return (new DateTimeImmutable('1899-12-30'))->modify('+' . (int) floor($serial) . ' days')->format('Y-m-d');
        }

        $text = trim((string) preg_replace('/^\(?\s*exp\.?\s*/i', '', trim((string) $raw)), " \t()");

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $text, $m)) {
            [$year, $month, $day] = [(int) $m[1], (int) $m[2], (int) $m[3]];
        } elseif (preg_match('/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})$/', $text, $m)) {
            [$day, $month, $year] = [(int) $m[1], (int) $m[2], (int) $m[3]];
        } elseif (preg_match('/^(\d{1,2})[\/\-.](\d{2}|\d{4})$/', $text, $m)) {
            $month = (int) $m[1];
            $year = strlen($m[2]) === 2 ? 2000 + (int) $m[2] : (int) $m[2];
            if ($month < 1 || $month > 12) {
                $errors[] = $invalid;

                return null;
            }
            $day = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        } else {
            $errors[] = $invalid;

            return null;
        }

        if (! checkdate($month, $day, $year)) {
            $errors[] = $label . ' bukan tanggal yang valid.';

            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
