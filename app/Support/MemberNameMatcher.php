<?php

namespace App\Support;

/**
 * Mencocokkan nama "seadanya" di daftar panitia ke data anggota.
 *
 * Status hasil:
 *  - exact     : sama persis setelah dinormalisasi (huruf kecil, tanpa titik, "Mhd" = "Muhammad")
 *  - similar   : semua kata ada di satu anggota (boleh singkatan/awalan/typo 1 huruf) dan hanya satu kandidat terbaik
 *  - review    : ada kandidat tapi tidak cukup yakin (nama cuma 1 kata, atau beberapa kandidat sama kuat)
 *  - none      : tidak ada yang mirip
 */
class MemberNameMatcher
{
    /** @var array<int, array{id: int, name: string, student_id: ?string, norm: string, tokens: array<int,string>}> */
    private array $members = [];

    /** @param iterable<array{id: int, name: string, student_id?: ?string}> $members */
    public function __construct(iterable $members)
    {
        foreach ($members as $m) {
            $norm = self::normalize($m['name']);
            $this->members[] = [
                'id' => (int) $m['id'],
                'name' => $m['name'],
                'student_id' => $m['student_id'] ?? null,
                'norm' => $norm,
                'tokens' => $norm === '' ? [] : explode(' ', $norm),
            ];
        }
    }

    public static function normalize(string $name): string
    {
        $name = mb_strtolower($name);
        $name = strtr($name, ['ı' => 'i', 'İ' => 'i', 'é' => 'e', 'è' => 'e', 'ñ' => 'n']);
        $name = preg_replace('/[^a-z0-9]+/u', ' ', $name);
        $tokens = array_values(array_filter(explode(' ', trim($name)), fn ($t) => $t !== ''));

        foreach ($tokens as $i => $t) {
            // Singkatan "Muhammad": mhd / muhd / md / m (hanya kata pertama untuk "m")
            if (in_array($t, ['mhd', 'muhd', 'md', 'muhammad', 'mohammad', 'mohamad', 'muhamad'], true) || ($t === 'm' && $i === 0)) {
                $tokens[$i] = 'muhammad';
            }
        }

        return implode(' ', $tokens);
    }

    /** @return array{status: string, candidates: array<int, array{id: int, name: string, student_id: ?string}>} */
    public function match(string $input): array
    {
        $norm = self::normalize($input);
        if ($norm === '') {
            return ['status' => 'none', 'candidates' => []];
        }

        $exact = array_values(array_filter($this->members, fn ($m) => $m['norm'] === $norm));
        if (count($exact) === 1) {
            return ['status' => 'exact', 'candidates' => [$this->pub($exact[0])]];
        }
        if (count($exact) > 1) {
            return ['status' => 'review', 'candidates' => array_map([$this, 'pub'], $exact)];
        }

        $tokens = explode(' ', $norm);
        $scored = [];
        foreach ($this->members as $m) {
            $score = $this->score($tokens, $m['tokens']);
            if ($score !== null) {
                $scored[] = ['m' => $m, 'score' => $score, 'extra' => count($m['tokens']) - count($tokens)];
            }
        }
        if (! $scored) {
            // Arah sebaliknya: nama di data lebih pendek dari yang diketik
            // (mis. "Prabu Rivali Fhatan Meliala" vs data "Rival Fhatan").
            $reverse = [];
            foreach ($this->members as $m) {
                if (count($m['tokens']) >= 2 && count($m['tokens']) < count($tokens)) {
                    $score = $this->score($m['tokens'], $tokens);
                    if ($score !== null && $score >= 0.8) {
                        $reverse[] = $m;
                    }
                }
            }
            if (count($reverse) === 1) {
                return ['status' => 'similar', 'candidates' => [$this->pub($reverse[0])]];
            }
            if ($reverse) {
                return ['status' => 'review', 'candidates' => array_map([$this, 'pub'], array_slice($reverse, 0, 4))];
            }

            return ['status' => 'none', 'candidates' => []];
        }

        usort($scored, fn ($a, $b) => [$b['score'], $a['extra']] <=> [$a['score'], $b['extra']]);
        $cands = array_map(fn ($s) => $this->pub($s['m']), array_slice($scored, 0, 4));

        $top = $scored[0];
        $tie = isset($scored[1]) && abs($scored[1]['score'] - $top['score']) < 0.0001 && $scored[1]['extra'] === $top['extra'];
        // Nama 1 kata (mis. "Riswana") dianggap cukup yakin bila TEPAT satu anggota
        // memiliki kata itu persis (bukan sekadar awalan/typo). Kalau ada dua+, tetap "review".
        $strongEnough = count($tokens) >= 2
            ? $top['score'] >= 0.8
            : $top['score'] >= 1.0 && ! $tie && (! isset($scored[1]) || $scored[1]['score'] < 1.0);

        if (! $tie && $strongEnough && (! isset($scored[1]) || $top['score'] > $scored[1]['score'] || $top['extra'] < $scored[1]['extra'])) {
            return ['status' => 'similar', 'candidates' => $cands];
        }

        return ['status' => 'review', 'candidates' => $cands];
    }

    /** Cari langsung lewat NIM atau nama lengkap persis (untuk file alias). */
    public function find(string $needle): ?array
    {
        $needle = trim($needle);
        foreach ($this->members as $m) {
            if ($m['student_id'] !== null && $m['student_id'] !== '' && $m['student_id'] === $needle) {
                return $this->pub($m);
            }
        }
        $norm = self::normalize($needle);
        foreach ($this->members as $m) {
            if ($m['norm'] === $norm) {
                return $this->pub($m);
            }
        }

        return null;
    }

    /**
     * Setiap kata input harus cocok dengan kata anggota yang berbeda.
     * Bobot: sama 1.0, awalan 0.9, typo 1-2 huruf 0.8. null = ada kata yang tak cocok.
     */
    private function score(array $inTokens, array $mTokens): ?float
    {
        if (! $mTokens) {
            return null;
        }
        $available = $mTokens;
        $total = 0.0;

        foreach ($inTokens as $t) {
            $bestIdx = null;
            $bestW = 0.0;
            foreach ($available as $idx => $mt) {
                $w = $this->tokenWeight($t, $mt);
                if ($w > $bestW) {
                    $bestW = $w;
                    $bestIdx = $idx;
                }
            }
            if ($bestIdx === null) {
                return null;
            }
            $total += $bestW;
            unset($available[$bestIdx]);
        }

        return $total / count($inTokens);
    }

    private function tokenWeight(string $a, string $b): float
    {
        if ($a === $b) {
            return 1.0;
        }
        $la = strlen($a);
        $lb = strlen($b);
        if ($la >= 3 && $lb >= 3 && (str_starts_with($b, $a) || str_starts_with($a, $b))) {
            return 0.9;
        }
        $lev = levenshtein($a, $b);
        if ($lev === 1 && min($la, $lb) >= 4) {
            return 0.85;
        }
        if ($lev === 2 && min($la, $lb) >= 7) {
            return 0.8;
        }

        return 0.0;
    }

    private function pub(array $m): array
    {
        return ['id' => $m['id'], 'name' => $m['name'], 'student_id' => $m['student_id']];
    }
}
