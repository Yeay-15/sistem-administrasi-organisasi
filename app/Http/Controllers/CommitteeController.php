<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AuditLog;
use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\Member;
use App\Models\PanitiaBidang;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommitteeController extends Controller
{
    public function index(Request $request)
    {
        $query = Committee::withCount('committeeMembers');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $committees = $query->orderByDesc('start_date')->orderByDesc('id')->get();

        return view('committees.index', compact('committees'));
    }

    public function create()
    {
        return view('committees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Persiapan,Berjalan,Selesai'],
        ]);

        $committee = Committee::create($validated);

        AuditLog::record('Tambah Kepanitiaan', "Membuat kepanitiaan baru \"{$committee->name}\".");

        return redirect()->route('kepanitiaan.show', $committee)->with('success', 'Kepanitiaan berhasil dibuat. Sekarang tambahkan anggota panitianya.');
    }

    public function show(Committee $kepanitiaan)
    {
        $kepanitiaan->load([
            'committeeMembers' => fn ($q) => $q->with(['member', 'bidang'])->orderBy('position_category'),
            'agendas',
        ]);

        $terasPanitia = $kepanitiaan->committeeMembers->filter->isTerasPanitia();
        $anggotaBidang = $kepanitiaan->committeeMembers->reject->isTerasPanitia()->groupBy(fn ($cm) => $cm->bidang->name ?? 'Tanpa Bidang');

        $bidangList = PanitiaBidang::orderBy('name')->get();

        // Untuk dropdown "pilih orang yang sudah ada" — semua members (Pengurus
        // & Non-Pengurus) yang BELUM terdaftar sebagai anggota di kepanitiaan ini,
        // supaya tidak bisa ditambahkan dobel lewat form yang sama.
        $existingMemberIds = $kepanitiaan->committeeMembers->pluck('member_id')->unique();
        $availableMembers = Member::whereNotIn('id', $existingMemberIds)
            ->orderBy('membership_type')
            ->orderBy('name')
            ->get(['id', 'name', 'membership_type', 'student_id']);

        // Untuk mengaitkan agenda (rapat persiapan + hari-H) — agenda yang
        // belum ditautkan ke kepanitiaan ini, terbaru dulu.
        $linkedAgendaIds = $kepanitiaan->agendas->pluck('id');
        $availableAgendas = Agenda::whereNotIn('id', $linkedAgendaIds)->orderByDesc('date')->limit(100)->get();

        return view('committees.show', compact(
            'kepanitiaan',
            'terasPanitia',
            'anggotaBidang',
            'bidangList',
            'availableMembers',
            'availableAgendas'
        ));
    }

    public function edit(Committee $kepanitiaan)
    {
        return view('committees.edit', ['committee' => $kepanitiaan]);
    }

    public function update(Request $request, Committee $kepanitiaan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Persiapan,Berjalan,Selesai'],
        ]);

        $kepanitiaan->update($validated);

        AuditLog::record('Ubah Kepanitiaan', "Memperbarui data kepanitiaan \"{$kepanitiaan->name}\".");

        return redirect()->route('kepanitiaan.show', $kepanitiaan)->with('success', 'Kepanitiaan berhasil diperbarui.');
    }

    public function destroy(Committee $kepanitiaan)
    {
        AuditLog::record('Hapus Kepanitiaan', "Menghapus kepanitiaan \"{$kepanitiaan->name}\".");

        $kepanitiaan->delete();

        return redirect()->route('kepanitiaan.index')->with('success', 'Kepanitiaan berhasil dihapus.');
    }

    /**
     * Tambah satu baris anggota panitia (member + peran [+ bidang]).
     * member_id boleh berasal dari orang yang sudah ada di sistem
     * (Pengurus atau Non-Pengurus) — untuk mendaftarkan orang yang
     * benar-benar baru, form memakai quickCreateMember() lebih dulu.
     */
    public function addMember(Request $request, Committee $kepanitiaan)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            // Tidak dibatasi Rule::in lagi — peran boleh dari daftar siap pakai
            // ATAU diketik bebas lewat opsi "Lainnya" di form (lihat
            // Committee::positionCategories() untuk daftar siap pakainya).
            'position_category' => ['required', 'string', 'max:100'],
            'panitia_bidang_id' => ['nullable', 'exists:panitia_bidang,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $isBidangRole = in_array($validated['position_category'], ['Ketua Bidang', 'Anggota Bidang'], true);

        if ($isBidangRole && empty($validated['panitia_bidang_id'])) {
            throw ValidationException::withMessages([
                'panitia_bidang_id' => 'Pilih bidang panitia untuk peran ini.',
            ]);
        }

        if (! $isBidangRole) {
            $validated['panitia_bidang_id'] = null;
        }

        $validated['committee_id'] = $kepanitiaan->id;

        CommitteeMember::create($validated);

        $member = Member::find($validated['member_id']);
        AuditLog::record(
            'Tambah Anggota Kepanitiaan',
            "Menambahkan \"{$member->name}\" sebagai {$validated['position_category']} di kepanitiaan \"{$kepanitiaan->name}\"."
        );

        return redirect()->route('kepanitiaan.show', $kepanitiaan)->with('success', 'Anggota panitia berhasil ditambahkan.');
    }

    public function removeMember(Committee $kepanitiaan, CommitteeMember $committeeMember)
    {
        abort_unless($committeeMember->committee_id === $kepanitiaan->id, 404);

        $name = $committeeMember->member->name ?? '(orang telah dihapus)';
        $committeeMember->delete();

        AuditLog::record('Hapus Anggota Kepanitiaan', "Mengeluarkan \"{$name}\" dari kepanitiaan \"{$kepanitiaan->name}\".");

        return redirect()->route('kepanitiaan.show', $kepanitiaan)->with('success', 'Anggota panitia berhasil dikeluarkan.');
    }

    /**
     * "+ Tambah Orang Baru" dari form anggota panitia — membuat record
     * Member baru dengan membership_type Non-Pengurus secara instan (tanpa
     * pindah halaman), supaya orang luar (dosen pembimbing, panitia dari
     * kampus lain, dsb) tidak perlu didaftarkan lebih dulu lewat menu
     * Anggota Non-Pengurus sebelum bisa ditambahkan ke kepanitiaan.
     */
    public function quickCreateMember(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'university' => ['nullable', 'string', 'max:150'],
        ]);

        $member = Member::create([
            'name' => $validated['name'],
            'university' => $validated['university'] ?? null,
            'membership_type' => 'Non-Pengurus',
            'status' => 'Aktif',
        ]);

        return response()->json([
            'id' => $member->id,
            'name' => $member->name,
            'membership_type' => $member->membership_type,
        ]);
    }

    public function attachAgenda(Request $request, Committee $kepanitiaan)
    {
        $validated = $request->validate([
            'agenda_id' => ['required', 'exists:agendas,id'],
        ]);

        $kepanitiaan->agendas()->syncWithoutDetaching([$validated['agenda_id']]);

        return redirect()->route('kepanitiaan.show', $kepanitiaan)->with('success', 'Agenda berhasil dikaitkan ke kepanitiaan ini.');
    }

    public function detachAgenda(Committee $kepanitiaan, Agenda $agenda)
    {
        $kepanitiaan->agendas()->detach($agenda->id);

        return redirect()->route('kepanitiaan.show', $kepanitiaan)->with('success', 'Agenda berhasil dilepas dari kepanitiaan ini.');
    }
}
