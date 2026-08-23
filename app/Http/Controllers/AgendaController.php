<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Member;
use App\Models\Division;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Agenda::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('agenda_code', 'like', '%' . $request->search . '%');
        }

        $agendas = $query->orderBy('date', 'asc')->get();

        // Prepare data for the Alpine.js Calendar
        $calendarEvents = $agendas->map(function ($agenda) {
            $contextText = strtolower($agenda->name . ' ' . $agenda->person_in_charge);

            // Mapping of each division to its exact Tailwind CSS classes
            // We write the full class names so Tailwind JIT compiler doesn't purge them
            $divisionColors = [
                'kaderisasi' => ['bg' => 'bg-red-500', 'from' => 'from-red-500', 'to' => 'to-red-500'],
                'korwil' => ['bg' => 'bg-orange-500', 'from' => 'from-orange-500', 'to' => 'to-orange-500'],
                'koordinator' => ['bg' => 'bg-orange-500', 'from' => 'from-orange-500', 'to' => 'to-orange-500'],
                'humas' => ['bg' => 'bg-emerald-500', 'from' => 'from-emerald-500', 'to' => 'to-emerald-500'],
                'hubungan masyarakat' => ['bg' => 'bg-emerald-500', 'from' => 'from-emerald-500', 'to' => 'to-emerald-500'],
                'infokom' => ['bg' => 'bg-cyan-500', 'from' => 'from-cyan-500', 'to' => 'to-cyan-500'],
                'informasi' => ['bg' => 'bg-cyan-500', 'from' => 'from-cyan-500', 'to' => 'to-cyan-500'],
                'minat' => ['bg' => 'bg-pink-500', 'from' => 'from-pink-500', 'to' => 'to-pink-500'],
                'bakat' => ['bg' => 'bg-pink-500', 'from' => 'from-pink-500', 'to' => 'to-pink-500'],
                'agama' => ['bg' => 'bg-emerald-600', 'from' => 'from-emerald-600', 'to' => 'to-emerald-600'],
                'pendidikan' => ['bg' => 'bg-yellow-500', 'from' => 'from-yellow-500', 'to' => 'to-yellow-500'],
                'invent' => ['bg' => 'bg-slate-500', 'from' => 'from-slate-500', 'to' => 'to-slate-500'],
                'bph' => ['bg' => 'bg-blue-600', 'from' => 'from-blue-600', 'to' => 'to-blue-600'],
                'badan pengurus harian' => ['bg' => 'bg-blue-600', 'from' => 'from-blue-600', 'to' => 'to-blue-600'],
            ];

            // Find all matching divisions in the text
            $foundDivisions = [];
            foreach ($divisionColors as $keyword => $colors) {
                if (str_contains($contextText, $keyword)) {
                    // Use 'bg' as key to prevent duplicate color entries (e.g. Humas & Hubungan Masyarakat)
                    $foundDivisions[$colors['bg']] = $colors;
                }
            }

            // Re-index the array
            $foundDivisions = array_values($foundDivisions);

            $isCollab = str_contains($contextText, ' x ') ||
                str_contains($contextText, ' dan ') ||
                str_contains($contextText, '&') ||
                str_contains($contextText, 'kolaborasi');

            // Default color if nothing matches
            $colorClass = 'bg-blue-500';

            // Determine final color: Dynamic Gradient if Collab, otherwise solid color
            if ($isCollab && count($foundDivisions) >= 2) {
                // Combine 'from' color of the first division and 'to' color of the second division
                $colorClass = 'bg-gradient-to-r ' . $foundDivisions[0]['from'] . ' ' . $foundDivisions[1]['to'];
            } elseif (count($foundDivisions) > 0) {
                // Single division detected
                $colorClass = $foundDivisions[0]['bg'];
            }

            return [
                'id' => $agenda->id,
                'title' => $agenda->name,
                'code' => $agenda->agenda_code,
                'type' => $agenda->type,
                'pic' => $agenda->person_in_charge,
                'status' => $agenda->status,
                'date' => \Carbon\Carbon::parse($agenda->date)->format('Y-m-d'),
                'colorClass' => $colorClass,
            ];
        });

        return view('agendas.index', compact('agendas', 'calendarEvents'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('agendas.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agenda_code' => ['required', 'string', 'max:50', 'unique:agendas,agenda_code'],
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'max:100'],
            'person_in_charge' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        Agenda::create($request->all());

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function show(Agenda $agenda)
    {
        $members = Member::with('division')->where('status', 'Aktif')->get();
        $existingAttendances = $agenda->attendances->keyBy('member_id');

        return view('agendas.show', compact('agenda', 'members', 'existingAttendances'));
    }

    public function edit(Agenda $agenda)
    {
        $divisions = Division::all();
        return view('agendas.edit', compact('agenda', 'divisions'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $request->validate([
            'agenda_code' => ['required', 'string', 'max:50', 'unique:agendas,agenda_code,' . $agenda->id],
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', 'max:100'],
            'person_in_charge' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $agenda->update($request->all());

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return redirect()->route('agendas.index')->with('success', 'Agenda dan data absensinya berhasil dihapus.');
    }
}
