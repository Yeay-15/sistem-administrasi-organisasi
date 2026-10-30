<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AuditLog;
use App\Models\Committee;
use App\Models\CommitteeField;
use App\Models\CommitteeMember;
use App\Models\Member;
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
            'theme' => ['nullable', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'event_dates' => ['nullable', 'array', 'max:62'],
            'event_dates.*' => ['required', 'date_format:Y-m-d', 'distinct'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Persiapan,Berjalan,Selesai'],
        ]);

        $validated['event_dates'] = $this->normalizedEventDates($request);

        $committee = Committee::create($validated);

        AuditLog::record('Tambah Kepanitiaan', "Membuat kepanitiaan baru \"{$committee->name}\".");

        return redirect()->route('committees.show', $committee)->with('success', 'Kepanitiaan berhasil dibuat. Sekarang tambahkan anggota panitianya.');
    }

    /** Tanggal acara terurut tanpa duplikat; null bila kosong. */
    private function normalizedEventDates(Request $request): ?array
    {
        $dates = collect($request->input('event_dates', []))->filter()->unique()->sort()->values()->all();

        return $dates ?: null;
    }

    public function show(Committee $committee)
    {
        $committee->load([
            'committeeMembers' => fn ($q) => $q->with(['member', 'committeeField'])->orderBy('position_category'),
            'agendas',
            'outgoingLetters' => fn ($q) => $q->orderByDesc('date')->orderByDesc('id'),
        ]);

        // Urutan peran mengikuti Committee::positionCategories():
        // Ketua, Sekretaris, Bendahara Panitia, Bendahara Peserta, lalu Ketua Bidang sebelum Anggota Bidang.
        // (Sebelumnya orderBy('position_category') = urut abjad, jadi Bendahara muncul sebelum Ketua.)
        $roleOrder = array_flip(Committee::positionCategories());
        $sorted = $committee->committeeMembers
            ->sortBy(fn ($cm) => [$roleOrder[$cm->position_category] ?? 99, mb_strtolower($cm->member->name ?? '')])
            ->values();

        $committeeLeadership = $sorted->filter->isCommitteeLeadership()->values();
        $fieldMembers = $sorted->reject->isCommitteeLeadership()
            ->groupBy(fn ($cm) => $cm->committeeField->name ?? 'Tanpa Bidang')
            // Bidang mengikuti kolom "Urutan" di master Bidang Panitia (lalu abjad), "Tanpa Bidang" paling akhir;
            // urutan anggota di dalam bidang tetap (Ketua Bidang dulu).
            ->sortBy(fn ($list, $name) => [
                $name === 'Tanpa Bidang' ? 100000 : ($list->first()->committeeField->sort_order ?? 100),
                mb_strtolower($name),
            ]);

        $fields = CommitteeField::orderBy('sort_order')->orderBy('name')->get();

        // Untuk dropdown "pilih orang yang sudah ada" — semua members (Pengurus
        // & Non-Pengurus) yang BELUM terdaftar sebagai anggota di kepanitiaan ini,
        // supaya tidak bisa ditambahkan dobel lewat form yang sama.
        $existingMemberIds = $committee->committeeMembers->pluck('member_id')->unique();
        $availableMembers = Member::whereNotIn('id', $existingMemberIds)
            ->orderBy('membership_type')
            ->orderBy('name')
            ->get(['id', 'name', 'membership_type', 'student_id']);

        // Untuk mengaitkan agenda (rapat persiapan + hari-H) — agenda yang
        // belum ditautkan ke kepanitiaan ini, terbaru dulu.
        $linkedAgendaIds = $committee->agendas->pluck('id');
        $availableAgendas = Agenda::whereNotIn('id', $linkedAgendaIds)->orderByDesc('date')->limit(100)->get();

        return view('committees.show', compact(
            'committee',
            'committeeLeadership',
            'fieldMembers',
            'fields',
            'availableMembers',
            'availableAgendas'
        ));
    }

    public function edit(Committee $committee)
    {
        return view('committees.edit', compact('committee'));
    }

    public function update(Request $request, Committee $committee)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'theme' => ['nullable', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'event_dates' => ['nullable', 'array', 'max:62'],
            'event_dates.*' => ['required', 'date_format:Y-m-d', 'distinct'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Persiapan,Berjalan,Selesai'],
        ]);

        // Tanpa input dates (semua tanggal dihapus) harus tersimpan sebagai kosong.
        $validated['event_dates'] = $this->normalizedEventDates($request);

        $committee->update($validated);

        AuditLog::record('Ubah Kepanitiaan', "Memperbarui data kepanitiaan \"{$committee->name}\".");

        return redirect()->route('committees.show', $committee)->with('success', 'Kepanitiaan berhasil diperbarui.');
    }

    public function destroy(Committee $committee)
    {
        AuditLog::record('Hapus Kepanitiaan', "Menghapus kepanitiaan \"{$committee->name}\".");

        $committee->delete();

        return redirect()->route('committees.index')->with('success', 'Kepanitiaan berhasil dihapus.');
    }

    /**
     * Tambah satu baris anggota panitia (member + peran [+ bidang]).
     * member_id boleh berasal dari orang yang sudah ada di sistem
     * (Pengurus atau Non-Pengurus) — untuk mendaftarkan orang yang
     * benar-benar baru, form memakai quickCreateMember() lebih dulu.
     */
    public function addMember(Request $request, Committee $committee)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            // Tidak dibatasi Rule::in lagi — peran boleh dari daftar siap pakai
            // ATAU diketik bebas lewat opsi "Lainnya" di form (lihat
            // Committee::positionCategories() untuk daftar siap pakainya).
            'position_category' => ['required', 'string', 'max:100'],
            'committee_field_id' => ['nullable', 'exists:committee_fields,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $isFieldRole = in_array($validated['position_category'], ['Ketua Bidang', 'Anggota Bidang'], true);

        if ($isFieldRole && empty($validated['committee_field_id'])) {
            throw ValidationException::withMessages([
                'committee_field_id' => 'Pilih bidang panitia untuk peran ini.',
            ]);
        }

        if (! $isFieldRole) {
            $validated['committee_field_id'] = null;
        }

        $validated['committee_id'] = $committee->id;

        CommitteeMember::create($validated);

        $member = Member::find($validated['member_id']);
        AuditLog::record(
            'Tambah Anggota Kepanitiaan',
            "Menambahkan \"{$member->name}\" sebagai {$validated['position_category']} di kepanitiaan \"{$committee->name}\"."
        );

        return redirect()->route('committees.show', $committee)->with('success', 'Anggota panitia berhasil ditambahkan.');
    }

    public function removeMember(Committee $committee, CommitteeMember $committeeMember)
    {
        abort_unless($committeeMember->committee_id === $committee->id, 404);

        $name = $committeeMember->member->name ?? '(orang telah dihapus)';
        $committeeMember->delete();

        AuditLog::record('Hapus Anggota Kepanitiaan', "Mengeluarkan \"{$name}\" dari kepanitiaan \"{$committee->name}\".");

        return redirect()->route('committees.show', $committee)->with('success', 'Anggota panitia berhasil dikeluarkan.');
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

    public function attachAgenda(Request $request, Committee $committee)
    {
        $validated = $request->validate([
            'agenda_id' => ['required', 'exists:agendas,id'],
        ]);

        $committee->agendas()->syncWithoutDetaching([$validated['agenda_id']]);

        return redirect()->route('committees.show', $committee)->with('success', 'Agenda berhasil dikaitkan ke kepanitiaan ini.');
    }

    public function detachAgenda(Committee $committee, Agenda $agenda)
    {
        $committee->agendas()->detach($agenda->id);

        return redirect()->route('committees.show', $committee)->with('success', 'Agenda berhasil dilepas dari kepanitiaan ini.');
    }
}
