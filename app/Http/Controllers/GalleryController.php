<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::with('uploader')->latest()->paginate(15);
        return view('galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('galleries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'], // Max 3MB per foto
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Satu form bisa mengunggah banyak foto sekaligus; setiap foto jadi satu baris Gallery.
        foreach ($request->file('images') as $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('galleries', $filename, 'public');

            Gallery::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $path,
                'uploaded_by' => Auth::id(),
            ]);
        }

        return redirect()->route('galleries.index')->with('success', 'Foto berhasil ditambahkan ke galeri.');
    }

    public function edit(Gallery $gallery)
    {
        return view('galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $gallery->update($request->only(['title', 'description']));

        return redirect()->route('galleries.index')->with('success', 'Keterangan foto berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return redirect()->route('galleries.index')->with('success', 'Foto berhasil dihapus dari galeri.');
    }
}
