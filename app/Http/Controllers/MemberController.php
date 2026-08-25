<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index()
    {
        // Menggunakan with('division') untuk Eager Loading relasi divisi
        $members = Member::with('division')->latest()->get();
        return view('members.index', compact('members'));
    }

    public function create()
    {
        // Mengambil semua data divisi untuk ditampilkan di dropdown form
        $divisions = Division::all();
        return view('members.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50', Rule::unique('members', 'student_id')->whereNull('deleted_at')],
            'batch' => ['required', 'string', 'max:10'],
            'major' => ['nullable', 'string', 'max:150'],
            'university' => ['nullable', 'string', 'max:150'],
            'division_id' => ['required', 'exists:divisions,id'],
            'position' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string'],
            'join_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Max 2MB
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->storeMemberPhoto($request);
        }

        Member::create($data);

        return redirect()->route('members.index')->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function edit(Member $member)
    {
        $divisions = Division::all();
        return view('members.edit', compact('member', 'divisions'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Pengecualian validasi unique untuk ID pengurus yang sedang diedit
            'student_id' => ['required', 'string', 'max:50', 'unique:members,student_id,' . $member->id],
            'batch' => ['required', 'string', 'max:10'],
            'major' => ['nullable', 'string', 'max:150'],
            'university' => ['nullable', 'string', 'max:150'],
            'division_id' => ['required', 'exists:divisions,id'],
            'position' => ['required', 'string', 'in:Ketua Umum,Sekretaris Umum,Bendahara Umum,Ketua Divisi,Sekretaris Divisi,Anggota Divisi'],
            'status' => ['required', 'string'],
            'join_date' => ['required', 'date'],
            'exit_date' => ['nullable', 'date', 'after_or_equal:join_date'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        $data = $request->except(['photo', 'remove_photo']);

        if ($request->hasFile('photo')) {
            // Hapus foto lama sebelum menyimpan yang baru
            $this->deleteMemberPhoto($member);
            $data['photo_path'] = $this->storeMemberPhoto($request);
        } elseif ($request->boolean('remove_photo')) {
            // Admin memilih untuk menghapus foto tanpa menggantinya
            $this->deleteMemberPhoto($member);
            $data['photo_path'] = null;
        }

        $member->update($data);

        return redirect()->route('members.index')->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $this->deleteMemberPhoto($member);

        $member->delete();
        return redirect()->route('members.index')->with('success', 'Data pengurus berhasil dihapus (Soft Delete).');
    }

    /**
     * Simpan file foto yang diunggah ke storage/app/public/members
     * dan kembalikan path relatifnya untuk disimpan ke kolom photo_path.
     */
    private function storeMemberPhoto(Request $request): string
    {
        $filename = Str::uuid() . '.' . $request->file('photo')->getClientOriginalExtension();
        return $request->file('photo')->storeAs('members', $filename, 'public');
    }

    /**
     * Hapus file foto pengurus dari storage jika ada.
     */
    private function deleteMemberPhoto(Member $member): void
    {
        if ($member->photo_path && Storage::disk('public')->exists($member->photo_path)) {
            Storage::disk('public')->delete($member->photo_path);
        }
    }
}
