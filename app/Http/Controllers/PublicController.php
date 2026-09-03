<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Agenda;
use App\Models\Aspiration;
use App\Models\Division;
use App\Models\Gallery;
use App\Models\HomeSetting;
use App\Models\Member;
use App\Models\OrganizationLeader;
use App\Models\Post;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $settings = HomeSetting::current();

        // Dipisah agar Beranda menampilkan Laporan Kegiatan dan Artikel & Berita
        // sebagai dua bagian yang berbeda (mengikuti pola situs Ormawa UT),
        // bukan digabung jadi satu daftar "Berita Terbaru".
        $latestReports = Post::published()->category('Laporan Kegiatan')->latest('published_at')->take(3)->get();
        $latestArticles = Post::published()->category('Artikel & Berita')->latest('published_at')->take(3)->get();

        $upcomingAgendas = Agenda::publicOnly()->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->take(3)
            ->get();

        $achievements = Achievement::orderBy('order')->latest()->take(8)->get();

        $totalMembers = Member::where('status', 'Aktif')->count();

        return view('public.home', compact(
            'settings',
            'latestReports',
            'latestArticles',
            'upcomingAgendas',
            'achievements',
            'totalMembers'
        ));
    }

    // Profil > Tentang Kami
    public function about()
    {
        // Estafet Kepemimpinan — dikelola dari dashboard admin (menu
        // "Estafet Kepemimpinan"), diurutkan sesuai kolom "order" lalu
        // tahun mulai menjabat.
        $leaders = OrganizationLeader::orderBy('order')->orderBy('period_start')->get();

        return view('public.about', compact('leaders'));
    }

    // Profil > Visi & Misi
    public function vision()
    {
        return view('public.vision');
    }

    // Profil > Struktur Pengurus
    public function structure()
    {
        $divisions = Division::with(['members' => function ($query) {
            $query->where('status', 'Aktif')->orderByRaw("
                CASE position
                    WHEN 'Ketua Umum' THEN 1
                    WHEN 'Sekretaris Umum' THEN 2
                    WHEN 'Bendahara Umum' THEN 3
                    WHEN 'Ketua Divisi' THEN 4
                    WHEN 'Sekretaris Divisi' THEN 5
                    ELSE 6
                END
            ")->orderBy('name');
        }])->get();

        return view('public.structure', compact('divisions'));
    }

    // Agenda Kegiatan
    public function agenda(Request $request)
    {
        // Portal Publik hanya boleh menampilkan agenda yang ditandai
        // is_public — agenda internal (mis. rapat pembentukan panitia)
        // tetap ada di kalender admin tapi disaring di sini.
        $query = Agenda::publicOnly();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $upcomingAgendas = (clone $query)->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->paginate(9, ['*'], 'akan_datang')
            ->withQueryString();

        $pastAgendas = (clone $query)->where('date', '<', now()->toDateString())
            ->orderByDesc('date')
            ->paginate(6, ['*'], 'terlaksana')
            ->withQueryString();

        // Untuk tampilan kalender publik — memakai skema warna yang sama dengan admin
        $allAgendas = Agenda::publicOnly()->orderBy('date')->get();
        $calendarEvents = Agenda::buildCalendarEvents($allAgendas);

        return view('public.agenda', compact('upcomingAgendas', 'pastAgendas', 'calendarEvents'));
    }

    // Media > Artikel & Berita
    public function news(Request $request)
    {
        $query = Post::published()->category('Artikel & Berita')->latest('published_at');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(9)->withQueryString();

        $pageTitle = 'Artikel & Berita';
        $pageDescription = 'Opini, tips, dan kabar seputar KATIBER.';
        $routeShow = 'public.news.show';
        $routeIndex = 'public.news.index';

        return view('public.news.index', compact('posts', 'pageTitle', 'pageDescription', 'routeShow', 'routeIndex'));
    }

    public function newsShow(Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at?->isPast() && $post->category === 'Artikel & Berita', 404);

        $relatedPosts = Post::published()
            ->category('Artikel & Berita')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $routeIndex = 'public.news.index';
        $routeShow = 'public.news.show';

        return view('public.news.show', compact('post', 'relatedPosts', 'routeIndex', 'routeShow'));
    }

    // Media > Laporan Kegiatan (menggunakan view yang sama dengan Artikel & Berita)
    public function reports(Request $request)
    {
        $query = Post::published()->category('Laporan Kegiatan')->latest('published_at');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(9)->withQueryString();

        $pageTitle = 'Laporan Kegiatan';
        $pageDescription = 'Dokumentasi dan rekam jejak kegiatan KATIBER yang telah terlaksana.';
        $routeShow = 'public.reports.show';
        $routeIndex = 'public.reports.index';

        return view('public.news.index', compact('posts', 'pageTitle', 'pageDescription', 'routeShow', 'routeIndex'));
    }

    public function reportShow(Post $post)
    {
        abort_unless($post->status === 'published' && $post->published_at?->isPast() && $post->category === 'Laporan Kegiatan', 404);

        $relatedPosts = Post::published()
            ->category('Laporan Kegiatan')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $routeIndex = 'public.reports.index';
        $routeShow = 'public.reports.show';

        return view('public.news.show', compact('post', 'relatedPosts', 'routeIndex', 'routeShow'));
    }

    // Media > Galeri
    public function gallery()
    {
        $galleries = Gallery::latest()->paginate(24);

        return view('public.gallery', compact('galleries'));
    }

    // Kontak & Aspirasi
    public function contact()
    {
        $settings = HomeSetting::current();

        return view('public.contact', compact('settings'));
    }

    public function contactStore(Request $request)
    {
        $settings = HomeSetting::current();

        abort_if($settings->aspiration_mode === 'nonaktif', 403, 'Formulir aspirasi sedang dinonaktifkan.');
        abort_if($settings->aspiration_mode === 'pengurus_only' && !auth()->check(), 403, 'Silakan login sebagai pengurus untuk mengirim aspirasi internal.');

        $validated = $request->validate([
            'is_anonymous' => ['nullable', 'boolean'],
            'name' => ['nullable', 'required_if:is_anonymous,false', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'in:Aspirasi,Kritik Saran,Laporan,Pertanyaan'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        // Mode "Hanya Pengurus" hanya membatasi SIAPA yang boleh membuka
        // formulir (harus login) — bukan berarti melarang kirim anonim.
        // Login di sini berfungsi sebagai gerbang akses, bukan syarat
        // keterbukaan identitas, supaya pengurus tetap bisa menyampaikan
        // aspirasi internal secara anonim bila diperlukan.
        $isAnonymous = $request->boolean('is_anonymous');

        Aspiration::create([
            'name' => $isAnonymous ? null : ($validated['name'] ?? null),
            'contact' => $isAnonymous ? null : ($validated['contact'] ?? null),
            'category' => $validated['category'],
            'message' => $validated['message'],
            'is_anonymous' => $isAnonymous,
        ]);

        return back()->with('success', 'Terima kasih! Pesan Anda telah kami terima dan akan segera ditindaklanjuti.');
    }
}
