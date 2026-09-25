<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\Division;
use App\Exports\MembersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Query Builder dengan relasi.
        // Menu "Pengurus" hanya menampilkan membership_type = Pengurus —
        // anggota Non-Pengurus punya menu & controller terpisah (lihat
        // NonPengurusController) walau sama-sama menyimpan datanya di
        // tabel members.
        $query = Member::with('division')->where('membership_type', 'Pengurus');

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
        // Form ini khusus alur Pengurus — dipaksa terlepas dari input apa pun,
        // supaya tidak bisa disusupi jadi Non-Pengurus lewat form yang salah.
        $data['membership_type'] = 'Pengurus';

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
     * Halaman statistik individu — dipakai bersama oleh menu Pengurus
     * maupun Anggota Non-Pengurus (tombol "Lihat Statistik" di kedua
     * daftar mengarah ke sini), karena keduanya sama-sama row di tabel
     * members. Menampilkan histori kepanitiaan (dikelompokkan Teras
     * Panitia vs Bidang) dan statistik kehadiran agenda organisasi.
     */
    public function statistik(Member $member)
    {
        abort_unless(
            Auth::user()->isSuperAdmin()
                || Auth::user()->hasPermission('view_members')
                || Auth::user()->hasPermission('view_non_pengurus'),
            403
        );

        $member->load('division');

        // --- Histori Kepanitiaan ---
        $committeeMemberships = $member->committeeMemberships()
            ->with(['committee', 'bidang'])
            ->get()
            ->sortByDesc(fn ($cm) => $cm->committee->start_date ?? $cm->created_at);

        $terasPanitia = $committeeMemberships->filter->isTerasPanitia();
        $anggotaBidang = $committeeMemberships->reject->isTerasPanitia();

        // --- Statistik Kehadiran Agenda Organisasi (H/I/S/A) ---
        $totalAgenda = Agenda::count();
        $attendanceByStatus = Attendance::where('member_id', $member->id)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $totalHadir = (int) ($attendanceByStatus['H'] ?? 0);
        $persentaseKehadiran = $totalAgenda > 0 ? round(($totalHadir / $totalAgenda) * 100, 1) : 0;

        // Breakdown per jenis agenda (mis. Rapat vs Kegiatan) — memakai
        // kolom `type` yang sudah ada di tabel agendas supaya insight-nya
        // lebih tajam daripada cuma satu angka total.
        $attendanceByType = Attendance::where('attendances.member_id', $member->id)
            ->join('agendas', 'agendas.id', '=', 'attendances.agenda_id')
            ->where('attendances.status', 'H')
            ->selectRaw('agendas.type as type, COUNT(*) as jumlah')
            ->groupBy('agendas.type')
            ->pluck('jumlah', 'type');

        $totalAgendaByType = Agenda::selectRaw('type, COUNT(*) as jumlah')
            ->groupBy('type')
            ->pluck('jumlah', 'type');

        // --- Kehadiran khusus rapat-rapat tiap kepanitiaan yang diikuti ---
        $committeeAttendance = $committeeMemberships->map(function ($cm) use ($member) {
            $agendaIds = $cm->committee->agendas()->pluck('agendas.id');
            $hadir = Attendance::where('member_id', $member->id)
                ->whereIn('agenda_id', $agendaIds)
                ->where('status', 'H')
                ->count();

            return [
                'committee' => $cm->committee,
                'total_agenda' => $agendaIds->count(),
                'hadir' => $hadir,
            ];
        })->unique(fn ($row) => $row['committee']->id)->values();

        return view('members.statistik', compact(
            'member',
            'terasPanitia',
            'anggotaBidang',
            'totalAgenda',
            'totalHadir',
            'persentaseKehadiran',
            'attendanceByStatus',
            'attendanceByType',
            'totalAgendaByType',
            'committeeAttendance'
        ));
    }

    /**
     * Form konfirmasi sebelum menjadikan seseorang Pengurus — perlu
     * melengkapi divisi & jabatan dulu, jadi tidak bisa langsung toggle
     * lewat satu klik tombol seperti arah sebaliknya (Pengurus -> Non-Pengurus).
     */
    public function promoteForm(Member $member)
    {
        abort_unless($member->membership_type === 'Non-Pengurus', 404);

        $divisions = Division::all();

        return view('non_pengurus.promote', compact('member', 'divisions'));
    }

    /**
     * Pindahkan status keanggotaan Pengurus <-> Non-Pengurus.
     *
     * Saat dipindah jadi Pengurus, divisi & jabatan WAJIB dilengkapi lewat
     * form ini (karena kolomnya nullable di DB, tapi tetap wajib secara
     * fungsional untuk pengurus). Saat dipindah jadi Non-Pengurus, data
     * divisi/jabatan sengaja TIDAK dihapus — hanya disembunyikan dari
     * tampilan Pengurus aktif — supaya kalau orang itu jadi pengurus lagi
     * nanti, datanya tidak perlu diisi ulang dari nol. Histori kepanitiaan
     * & kehadiran tidak pernah ikut berubah karena tetap merujuk ke
     * member_id yang sama.
     */
    public function toggleMembershipType(Request $request, Member $member)
    {
        abort_unless(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('manage_members'), 403);

        if ($member->membership_type === 'Pengurus') {
            $member->update(['membership_type' => 'Non-Pengurus']);

            return redirect()->back()->with('success', "\"{$member->name}\" dipindahkan ke Anggota Non-Pengurus.");
        }

        $request->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'position' => ['required', 'string', 'max:100'],
            'batch' => ['required', 'string', 'max:10'],
            'join_date' => ['required', 'date'],
        ]);

        $member->update([
            'membership_type' => 'Pengurus',
            'division_id' => $request->division_id,
            'position' => $request->position,
            'batch' => $request->batch,
            'join_date' => $request->join_date,
        ]);

        return redirect()->route('members.index')->with('success', "\"{$member->name}\" dijadikan Pengurus.");
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
