<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::orderBy('order')->latest()->paginate(12);

        return view('achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('achievements.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $data = [
            'title' => $validated['title'],
            'achiever_name' => $validated['achiever_name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
        ];

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request);
        }

        Achievement::create($data);

        return redirect()->route('achievements.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(Achievement $achievement)
    {
        return view('achievements.edit', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $validated = $this->validateRequest($request);

        $data = [
            'title' => $validated['title'],
            'achiever_name' => $validated['achiever_name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
        ];

        if ($request->hasFile('photo')) {
            $this->deletePhoto($achievement);
            $data['photo_path'] = $this->storePhoto($request);
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($achievement);
            $data['photo_path'] = null;
        }

        $achievement->update($data);

        return redirect()->route('achievements.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement)
    {
        $this->deletePhoto($achievement);
        $achievement->delete();

        return redirect()->route('achievements.index')->with('success', 'Prestasi berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'achiever_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
    }

    private function storePhoto(Request $request): string
    {
        $filename = Str::uuid() . '.' . $request->file('photo')->getClientOriginalExtension();
        return $request->file('photo')->storeAs('achievements', $filename, 'public');
    }

    private function deletePhoto(Achievement $achievement): void
    {
        if ($achievement->photo_path && Storage::disk('public')->exists($achievement->photo_path)) {
            Storage::disk('public')->delete($achievement->photo_path);
        }
    }
}
