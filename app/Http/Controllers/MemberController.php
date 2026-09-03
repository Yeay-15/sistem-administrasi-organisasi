<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Division;
use App\Exports\MembersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Query Builder dengan relasi
        $query = Member::with('division');

        // 2. Cek dan terapkan filter Divisi jika dipilih
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        // 3. Cek dan terapkan filter Angkatan jika dipilih
        if ($request->filled('batch')) {
            $query->where('batch', $request->batch);
        }

        // 4. Lakukan pengurutan hierarki seperti biasa, lalu ambil datanya (get)
        $members = $query->orderBy('division_id', 'asc')
            ->orderByRaw("
                CASE position
                    WHEN 'Ketua Umum' THEN 1
                    WHEN 'Sekretaris Umum' THEN 2
                    WHEN 'Bendahara Umum' THEN 3
                    WHEN 'Ketua Divisi' THEN 4
                    WHEN 'Sekretaris Divisi' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('name', 'asc')
            ->get();

        // 5. Fitur Export (Otomatis akan mengekspor data yang sudah difilter saja)
        if ($request->export === 'excel') {
            return Excel::download(new MembersExport($members), 'Data_Pengurus_KATIBER.xlsx');
        }

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('exports.members_pdf', compact('members'))->setPaper('a4', 'landscape');
            return $pdf->download('Data_Pengurus_KATIBER.pdf');
        }

        // 6. Ambil data untuk opsi Dropdown Filter di View
        $divisions = Division::all();
        // Mengambil daftar angkatan yang unik dari tabel members (misal: 2023, 2024)
        $batches = Member::select('batch')->distinct()->orderBy('batch', 'desc')->pluck('batch');

        return view('members.index', compact('members', 'divisions', 'batches'));
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
