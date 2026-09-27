<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Member;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Agenda::with('divisions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('agenda_code', 'like', '%' . $request->search . '%');
        }

        $agendas = $query->orderBy('date', 'asc')->get();

        // Prepare data for the Alpine.js Calendar (dan juga dipakai ulang untuk cetak PDF kalender)
        $calendarEvents = Agenda::buildCalendarEvents($agendas);

        return view('agendas.index', compact('agendas', 'calendarEvents'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('agendas.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agenda_code' => ['required', 'string', 'max:50', 'unique:agendas,agenda_code'],
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'max:100'],
            'person_in_charge' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'attendance_scope' => ['required', 'in:all,division'],
            'division_ids' => ['required_if:attendance_scope,division', 'array'],
            'division_ids.*' => ['exists:divisions,id'],
        ]);

        $agenda = Agenda::create(array_merge($validated, [
            // Checkbox HTML tidak mengirim apa pun saat tidak dicentang,
            // jadi statusnya dihitung eksplisit di sini alih-alih ikut
            // $request->all() begitu saja.
            'is_public' => $request->boolean('is_public', true),
        ]));

        $agenda->divisions()->sync(
            $validated['attendance_scope'] === 'division' ? ($validated['division_ids'] ?? []) : []
        );

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function show(Agenda $agenda)
    {
        $agenda->load('divisions');

        // Kalau cakupannya 'division', daftar absensi hanya berisi anggota
        // dari divisi yang dicantumkan — bukan seluruh pengurus aktif —
        // supaya pengurus divisi lain yang memang tidak diundang tidak ikut
        // "kena absen alpha" di statistik kehadirannya.
        $members = Member::with('division')->where('status', 'Aktif');

        if (! $agenda->isForAllMembers()) {
            $members->whereIn('division_id', $agenda->divisions->pluck('id'));
        }

        $members = $members->get();
        $existingAttendances = $agenda->attendances->keyBy('member_id');

        return view('agendas.show', compact('agenda', 'members', 'existingAttendances'));
    }

    public function edit(Agenda $agenda)
    {
        $divisions = Division::all();
        $agenda->load('divisions');
        return view('agendas.edit', compact('agenda', 'divisions'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            'agenda_code' => ['required', 'string', 'max:50', 'unique:agendas,agenda_code,' . $agenda->id],
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'max:100'],
            'person_in_charge' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'attendance_scope' => ['required', 'in:all,division'],
            'division_ids' => ['required_if:attendance_scope,division', 'array'],
            'division_ids.*' => ['exists:divisions,id'],
        ]);

        $agenda->update(array_merge($validated, [
            'is_public' => $request->boolean('is_public', true),
        ]));

        $agenda->divisions()->sync(
            $validated['attendance_scope'] === 'division' ? ($validated['division_ids'] ?? []) : []
        );

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    /**
     * Toggle cepat status tampil-ke-publik lewat ikon mata di daftar agenda —
     * tanpa perlu masuk ke form edit.
     */
    public function togglePublic(Agenda $agenda)
    {
        Gate::authorize('manage_agendas');

        $agenda->update(['is_public' => ! $agenda->is_public]);

        return back()->with('success', $agenda->is_public
            ? 'Agenda "' . $agenda->name . '" sekarang tampil ke publik.'
            : 'Agenda "' . $agenda->name . '" disembunyikan dari publik.');
    }

    public function destroy(Agenda $agenda)
    {
        // Contoh Gate::authorize eksplisit di level controller (Tahap 3).
        // Seluruh resource 'agendas' sudah dilindungi middleware 3-tier
        // (can:view_agendas / can:manage_agendas / can:delete_agendas) di
        // routes/web.php, jadi baris ini sifatnya defense-in-depth — pola yang
        // sama bisa dipakai di controller lain untuk aturan yang lebih spesifik per-aksi.
        Gate::authorize('delete_agendas');

        $agenda->delete();
        return redirect()->route('agendas.index')->with('success', 'Agenda dan data absensinya berhasil dihapus.');
    }

    /**
     * Ekspor agenda ke PDF, mengikuti tampilan yang sedang aktif di layar admin:
     * - view=table    -> tabel rekap agenda (perilaku lama)
     * - view=calendar -> kalender bulanan dengan warna sesuai divisi/gradient kolaborasi,
     *                    persis seperti yang terlihat di Portal Admin.
     */
    public function exportPdf(Request $request)
    {
        $query = Agenda::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('agenda_code', 'like', '%' . $request->search . '%');
        }

        $agendas = $query->orderBy('date', 'asc')->get();

        if ($request->get('view') === 'calendar') {
            // month dikirim 0-indexed dari Alpine.js (JS Date convention)
            $month = (int) $request->get('month', now()->month - 1);
            $year = (int) $request->get('year', now()->year);

            $calendarEvents = Agenda::buildCalendarEvents($agendas);

            $pdf = Pdf::loadView('exports.agendas_calendar_pdf', compact('calendarEvents', 'month', 'year'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('Kalender_Agenda_KATIBER_' . ($month + 1) . '_' . $year . '.pdf');
        }

        $pdf = Pdf::loadView('exports.agendas_pdf', compact('agendas'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Agenda_KATIBER.pdf');
    }

}
