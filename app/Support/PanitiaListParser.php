<?php

namespace App\Support;

/**
 * Membaca daftar panitia format teks bebas, mis.:
 *
 *   Makrab:                          <- judul kepanitiaan (diakhiri titik dua)
 *   Ketua panitia: Nama              <- peran teras panitia
 *   Bendahara peserta: Nama
 *
 *   Acara                            <- judul bidang (baris tanpa angka & tanpa titik dua)
 *   1. Nama                          <- anggota bidang (bernomor)
 *   2. Nama
 *
 * Tanda centang (✅ ☑️ ✔ dst.) dan spasi berlebih diabaikan.
 */
class PanitiaListParser
{
    private const LEADERSHIP = [
        'ketua panitia' => 'Ketua Panitia',
        'sekretaris panitia' => 'Sekretaris Panitia',
        'bendahara panitia' => 'Bendahara Panitia',
        'bendahara peserta' => 'Bendahara Peserta',
    ];

    /**
     * @return array<int, array{title: string, entries: array<int, array{position: string, field: ?string, name: string, line: int}>}>
     */
    public static function parse(string $text): array
    {
        $sections = [];
        $current = null;
        $field = null;

        foreach (preg_split('/\R/u', $text) as $i => $rawLine) {
            $line = self::clean($rawLine);
            if ($line === '') {
                continue;
            }
            $lineNo = $i + 1;

            // Peran teras: "Ketua panitia: Nama"
            if (preg_match('/^(ketua|sekretaris|bendahara)\s+(panitia|peserta)\s*:\s*(.+)$/iu', $line, $m)) {
                $key = mb_strtolower($m[1] . ' ' . $m[2]);
                if ($current === null) {
                    throw new \RuntimeException("Baris {$lineNo}: peran \"{$line}\" muncul sebelum judul kepanitiaan (mis. \"Makrab:\").");
                }
                $current['entries'][] = [
                    'position' => self::LEADERSHIP[$key],
                    'field' => null,
                    'name' => trim($m[3]),
                    'line' => $lineNo,
                ];
                continue;
            }

            // Judul kepanitiaan: "Makrab:"
            if (preg_match('/^(.+?)\s*:$/u', $line, $m)) {
                if ($current !== null) {
                    $sections[] = $current;
                }
                $current = ['title' => trim($m[1]), 'entries' => []];
                $field = null;
                continue;
            }

            // Anggota bidang bernomor: "12. Nama" / "3) Nama"
            if (preg_match('/^\d+\s*[.)]\s*(.+)$/u', $line, $m)) {
                if ($current === null || $field === null) {
                    throw new \RuntimeException("Baris {$lineNo}: nama \"{$line}\" belum punya bidang/kepanitiaan di atasnya.");
                }
                $current['entries'][] = [
                    'position' => 'Anggota Bidang',
                    'field' => $field,
                    'name' => trim($m[1]),
                    'line' => $lineNo,
                ];
                continue;
            }

            // Selain itu = judul bidang
            $field = $line;
        }

        if ($current !== null) {
            $sections[] = $current;
        }

        return $sections;
    }

    /** Buang centang/emoji, selektor varian, spasi tak terlihat, dan spasi berlebih. */
    public static function clean(string $line): string
    {
        $line = preg_replace('/[\x{2600}-\x{27BF}\x{1F300}-\x{1FAFF}\x{FE0E}\x{FE0F}\x{200B}-\x{200D}\x{2705}\x{2611}\x{2714}]/u', '', $line);
        $line = str_replace("\xC2\xA0", ' ', $line);

        return trim(preg_replace('/\s+/u', ' ', $line));
    }
}
