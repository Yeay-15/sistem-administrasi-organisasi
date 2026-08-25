<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Division;
use App\Models\Gallery;
use App\Models\Member;
use App\Models\Post;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $latestPosts = Post::published()->latest('published_at')->take(3)->get();

        $upcomingAgendas = Agenda::where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->take(3)
            ->get();

        $totalMembers = Member::where('status', 'Aktif')->count();
        $totalDivisions = Division::count();

        return view('public.home', compact('latestPosts', 'upcomingAgendas', 'totalMembers', 'totalDivisions'));
    }

    public function about()
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

        return view('public.about', compact('divisions'));
    }

    public function news(Request $request)
    {
        $query = Post::published()->latest('published_at');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(9)->withQueryString();

        return view('public.news.index', compact('posts'));
    }

    public function newsShow(Post $post)
    {
        // Halaman baca hanya boleh diakses untuk berita yang benar-benar sudah terbit.
        abort_unless($post->status === 'published' && $post->published_at?->isPast(), 404);

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.news.show', compact('post', 'relatedPosts'));
    }

    public function gallery()
    {
        $galleries = Gallery::latest()->paginate(24);

        return view('public.gallery', compact('galleries'));
    }

    public function contact()
    {
        return view('public.contact');
    }
}
