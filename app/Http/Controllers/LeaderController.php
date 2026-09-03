<?php

namespace App\Http\Controllers;

use App\Models\OrganizationLeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeaderController extends Controller
{
    public function index()
    {
        $leaders = OrganizationLeader::orderBy('order')->orderBy('period_start')->paginate(12);

        return view('leaders.index', compact('leaders'));
    }

    public function create()
    {
        return view('leaders.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $data = [
            'name' => $validated['name'],
            'major' => $validated['major'] ?? null,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'order' => $validated['order'] ?? 0,
        ];

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storePhoto($request);
        }

        OrganizationLeader::create($data);

        return redirect()->route('leaders.index')->with('success', 'Data ketua umum berhasil ditambahkan.');
    }

    public function edit(OrganizationLeader $leader)
    {
        return view('leaders.edit', compact('leader'));
    }

    public function update(Request $request, OrganizationLeader $leader)
    {
        $validated = $this->validateRequest($request);

        $data = [
            'name' => $validated['name'],
            'major' => $validated['major'] ?? null,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'order' => $validated['order'] ?? 0,
        ];

        if ($request->hasFile('photo')) {
            $this->deletePhoto($leader);
            $data['photo_path'] = $this->storePhoto($request);
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($leader);
            $data['photo_path'] = null;
        }

        $leader->update($data);

        return redirect()->route('leaders.index')->with('success', 'Data ketua umum berhasil diperbarui.');
    }

    public function destroy(OrganizationLeader $leader)
    {
        $this->deletePhoto($leader);
        $leader->delete();

        return redirect()->route('leaders.index')->with('success', 'Data ketua umum berhasil dihapus.');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Sengaja nullable — sesuai permintaan, jurusan/kampus boleh
            // dikosongkan dulu kalau belum diketahui.
            'major' => ['nullable', 'string', 'max:255'],
            'period_start' => ['required', 'string', 'max:9'],
            'period_end' => ['required', 'string', 'max:9'],
            'order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
    }

    private function storePhoto(Request $request): string
    {
        $filename = Str::uuid() . '.' . $request->file('photo')->getClientOriginalExtension();
        return $request->file('photo')->storeAs('leaders', $filename, 'public');
    }

    private function deletePhoto(OrganizationLeader $leader): void
    {
        if ($leader->photo_path && Storage::disk('public')->exists($leader->photo_path)) {
            Storage::disk('public')->delete($leader->photo_path);
        }
    }
}
