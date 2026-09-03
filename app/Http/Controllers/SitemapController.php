<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    /**
     * Rute mana pun bernama "public.*" otomatis dianggap halaman publik
     * (masuk sitemap, tidak diblokir robots). Ini dicek sekali di banyak
     * tempat, jadi ditaruh sebagai satu konstanta.
     */
    private const PUBLIC_ROUTE_PREFIX = 'public.';

    /**
     * Nama-nama rute yang sengaja dikecualikan dari deteksi otomatis
     * robots.txt (bukan halaman admin, tapi juga bukan konten untuk
     * di-index — jadi tidak perlu masuk Disallow maupun sitemap).
     */
    private const IGNORED_ROUTE_NAMES = ['sitemap', 'robots'];

    /**
     * Prioritas & frekuensi update khusus untuk beberapa halaman utama —
     * di luar daftar ini, halaman publik baru otomatis dapat nilai default
     * yang wajar (lihat defaultRouteMeta()). Jadi menambah rute publik baru
     * TIDAK PERNAH butuh sentuhan manual di sini supaya muncul di sitemap;
     * daftar ini murni untuk "mempercantik" prioritas beberapa halaman
     * penting saja.
     */
    private const ROUTE_META_OVERRIDES = [
        'public.home' => ['priority' => '1.0', 'changefreq' => 'weekly'],
        'public.about' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        'public.about.vision' => ['priority' => '0.6', 'changefreq' => 'yearly'],
        'public.about.structure' => ['priority' => '0.6', 'changefreq' => 'monthly'],
        'public.agenda.index' => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'public.news.index' => ['priority' => '0.8', 'changefreq' => 'daily'],
        'public.reports.index' => ['priority' => '0.8', 'changefreq' => 'daily'],
        'public.gallery' => ['priority' => '0.6', 'changefreq' => 'weekly'],
        'public.contact' => ['priority' => '0.5', 'changefreq' => 'yearly'],
    ];

    /**
     * Menghasilkan robots.txt secara dinamis (rute, bukan file statis)
     * dengan daftar Disallow yang OTOMATIS mengikuti rute yang benar-benar
     * terdaftar saat ini — bukan daftar yang ditulis tangan. Jadi kalau
     * nanti ditambah fitur/rute admin baru, ia otomatis ikut ter-Disallow
     * di sini selama nama rutenya TIDAK diawali "public." (konvensi yang
     * sudah dipakai konsisten sejak awal untuk semua rute Portal Publik).
     */
    public function robots(): Response
    {
        $disallowedPrefixes = $this->discoverNonPublicRoutePrefixes();

        $lines = [
            'User-agent: *',
            '',
            '# Izinkan foto kegiatan, poster agenda, logo, dan foto pengurus untuk',
            '# dirayapi supaya bisa muncul di Google Images — KECUALI dua folder di',
            '# bawah ini, karena berisi lampiran surat masuk/keluar organisasi yang',
            '# sifatnya internal (bukan untuk publik / mesin pencari). Ini murni soal',
            '# folder penyimpanan file, bukan rute, jadi tidak bisa dideteksi otomatis',
            '# dan sengaja tetap ditulis manual.',
            'Allow: /storage/',
            'Disallow: /storage/incoming_letters/',
            'Disallow: /storage/outgoing_letters/',
            '',
            '# Halaman admin & autentikasi — terdeteksi OTOMATIS dari semua rute',
            '# yang namanya TIDAK diawali "public." (lihat SitemapController).',
        ];

        foreach ($disallowedPrefixes as $prefix) {
            $lines[] = "Disallow: /{$prefix}";
        }

        $lines[] = '';
        $lines[] = 'Sitemap: ' . url('/sitemap.xml');

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Menghasilkan sitemap.xml — berisi (1) semua rute publik STATIS
     * (tanpa parameter di URL-nya) yang terdeteksi otomatis dari daftar
     * rute bernama "public.*", ditambah (2) setiap Artikel & Berita /
     * Laporan Kegiatan yang sudah dipublikasikan (rute berparameter,
     * jadi perlu di-generate per baris data, bukan sekadar dibaca dari
     * daftar rute).
     */
    public function index(): Response
    {
        $urls = $this->discoverPublicStaticRoutes()
            ->merge($this->publishedPostUrls());

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Memindai seluruh rute terdaftar, ambil yang namanya diawali "public."
     * DAN tidak punya parameter di URL-nya (mis. {post}) — rute berparameter
     * butuh data konkret (lihat publishedPostUrls()) jadi tidak bisa asal
     * dipanggil route() tanpa argumen.
     */
    private function discoverPublicStaticRoutes(): Collection
    {
        return collect(Route::getRoutes())
            ->filter(function ($route) {
                $name = $route->getName();

                return $name
                    && str_starts_with($name, self::PUBLIC_ROUTE_PREFIX)
                    && in_array('GET', $route->methods())
                    && ! str_contains($route->uri(), '{');
            })
            ->unique(fn ($route) => $route->getName())
            ->map(function ($route) {
                $meta = self::ROUTE_META_OVERRIDES[$route->getName()] ?? $this->defaultRouteMeta();

                return [
                    'loc' => route($route->getName()),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => $meta['changefreq'],
                    'priority' => $meta['priority'],
                ];
            })
            ->values();
    }

    /**
     * Bagian yang TIDAK bisa full-otomatis dari daftar rute saja: rute
     * "show" per-artikel/laporan butuh slug tiap Post untuk dibentuk jadi
     * URL konkret. Kalau nanti ada jenis konten publik lain yang juga
     * punya halaman detail sendiri (rute berparameter baru), tinggal
     * tambah satu blok serupa di sini.
     */
    private function publishedPostUrls(): Collection
    {
        return Post::published()->orderByDesc('published_at')->get()->map(function (Post $post) {
            $routeName = $post->category === 'Laporan Kegiatan' ? 'public.reports.show' : 'public.news.show';

            return [
                'loc' => route($routeName, $post->slug),
                'lastmod' => $post->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        });
    }

    private function defaultRouteMeta(): array
    {
        return ['priority' => '0.6', 'changefreq' => 'monthly'];
    }

    /**
     * Memindai seluruh rute terdaftar, kembalikan segmen path PALING DEPAN
     * dari setiap rute yang namanya TIDAK diawali "public." (dan bukan
     * rute sitemap/robots itu sendiri) — dipakai sebagai baris Disallow.
     * Cukup segmen pertama karena itu sudah memblokir seluruh rute di
     * bawah prefix yang sama (mis. "members" memblokir "/members",
     * "/members/5/edit", dst sekaligus).
     */
    private function discoverNonPublicRoutePrefixes(): array
    {
        return collect(Route::getRoutes())
            ->filter(function ($route) {
                $name = $route->getName();

                if (! $name || in_array($name, self::IGNORED_ROUTE_NAMES)) {
                    return false;
                }

                if (str_starts_with($name, self::PUBLIC_ROUTE_PREFIX)) {
                    return false;
                }

                return in_array('GET', $route->methods());
            })
            ->map(function ($route) {
                $firstSegment = explode('/', trim($route->uri(), '/'))[0] ?? '';

                // Kalau segmen pertama itu sendiri berupa placeholder
                // parameter (jarang terjadi, tapi jaga-jaga), lewati saja
                // daripada menghasilkan baris "Disallow: /{sesuatu}" yang
                // tidak valid.
                return str_starts_with($firstSegment, '{') ? null : $firstSegment;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
