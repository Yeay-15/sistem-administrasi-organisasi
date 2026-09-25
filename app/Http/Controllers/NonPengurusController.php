<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Mengelola data orang yang ikut berkegiatan (mis. kepanitiaan) tapi bukan
 * bagian dari struktur Pengurus organisasi — dosen pembimbing, alumni,
 * mahasiswa lain, atau relawan lepas. Disimpan di tabel `members` yang sama
 * (kolom `membership_type` = 'Non-Pengurus') supaya histori kepanitiaan &
 * statistiknya tetap konsisten dengan data Pengurus, tapi punya menu &
 * form tersendiri yang lebih ringkas (tanpa divisi/jabatan/angkatan wajib).
 */
class NonPengurusController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::where('membership_type', 'Non-Pengurus');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $members = $query->orderBy('name', 'asc')->get();

        return view('non_pengurus.index', compact('members'));
    }

    public function create()
    {
        return view('non_pengurus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'university' => ['nullable', 'string', 'max:150'],
            'major' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = $request->only(['name', 'student_id', 'university', 'major', 'notes']);
        $data['membership_type'] = 'Non-Pengurus';
        $data['status'] = 'Aktif';

        if ($request->hasFile('photo')) {
            $filename = Str::uuid() . '.' . $request->file('photo')->getClientOriginalExtension();
            $data['photo_path'] = $request->file('photo')->storeAs('members', $filename, 'public');
        }

        Member::create($data);

        return redirect()->route('non-pengurus.index')->with('success', 'Data anggota non-pengurus berhasil ditambahkan.');
    }

    public function edit(Member $non_pengurus)
    {
        abort_unless($non_pengurus->membership_type === 'Non-Pengurus', 404);

        return view('non_pengurus.edit', ['member' => $non_pengurus]);
    }

    public function update(Request $request, Member $non_pengurus)
    {
        abort_unless($non_pengurus->membership_type === 'Non-Pengurus', 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'university' => ['nullable', 'string', 'max:150'],
            'major' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        $data = $request->only(['name', 'student_id', 'university', 'major', 'notes']);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($non_pengurus);
            $filename = Str::uuid() . '.' . $request->file('photo')->getClientOriginalExtension();
            $data['photo_path'] = $request->file('photo')->storeAs('members', $filename, 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($non_pengurus);
            $data['photo_path'] = null;
        }

        $non_pengurus->update($data);

        return redirect()->route('non-pengurus.index')->with('success', 'Data anggota non-pengurus berhasil diperbarui.');
    }

    public function destroy(Member $non_pengurus)
    {
        abort_unless($non_pengurus->membership_type === 'Non-Pengurus', 404);

        $this->deletePhoto($non_pengurus);
        $non_pengurus->delete();

        return redirect()->route('non-pengurus.index')->with('success', 'Data anggota non-pengurus berhasil dihapus.');
    }

    private function deletePhoto(Member $member): void
    {
        if ($member->photo_path && Storage::disk('public')->exists($member->photo_path)) {
            Storage::disk('public')->delete($member->photo_path);
        }
    }
}
