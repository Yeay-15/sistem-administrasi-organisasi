<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('author')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $data = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'slug' => $this->generateUniqueSlug($validated['title']),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'author_id' => Auth::id(),
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ];

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $this->storeCover($request);
        }

        Post::create($data);

        return redirect()->route('posts.index')->with('success', 'Berita berhasil disimpan.');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->validateRequest($request, $post->id);

        $data = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'status' => $validated['status'],
        ];

        // Regenerasi slug hanya jika judul berubah, supaya URL yang sudah dibagikan tetap valid.
        if ($validated['title'] !== $post->title) {
            $data['slug'] = $this->generateUniqueSlug($validated['title'], $post->id);
        }

        // published_at diisi begitu status pertama kali menjadi 'published',
        // tapi tidak direset ulang jika sudah pernah diterbitkan sebelumnya.
        if ($validated['status'] === 'published' && ! $post->published_at) {
            $data['published_at'] = now();
        } elseif ($validated['status'] === 'draft') {
            $data['published_at'] = null;
        }

        if ($request->hasFile('cover')) {
            $this->deleteCover($post);
            $data['cover_path'] = $this->storeCover($request);
        } elseif ($request->boolean('remove_cover')) {
            $this->deleteCover($post);
            $data['cover_path'] = null;
        }

        $post->update($data);

        return redirect()->route('posts.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $this->deleteCover($post);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Berita berhasil dihapus.');
    }

    private function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:Artikel & Berita,Laporan Kegiatan'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['nullable', 'boolean'],
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 2;

        while (
            Post::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function storeCover(Request $request): string
    {
        $filename = Str::uuid() . '.' . $request->file('cover')->getClientOriginalExtension();
        return $request->file('cover')->storeAs('posts', $filename, 'public');
    }

    private function deleteCover(Post $post): void
    {
        if ($post->cover_path && Storage::disk('public')->exists($post->cover_path)) {
            Storage::disk('public')->delete($post->cover_path);
        }
    }
}
