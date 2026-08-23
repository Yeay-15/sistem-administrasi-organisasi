<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, Agenda $agenda)
    {
        // Validasi input array dari tabel absensi
        $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['required', 'in:H,I,S,A'],
            'attendances.*.notes' => ['nullable', 'string'],
        ]);

        // Looping data yang dikirim dari form
        foreach ($request->attendances as $memberId => $data) {
            Attendance::updateOrCreate(
                // Kriteria pencarian data (Apakah absen pengurus ini di agenda ini sudah ada?)
                [
                    'agenda_id' => $agenda->id,
                    'member_id' => $memberId,
                ],
                // Jika sudah ada update ini, jika belum ada buat baru dengan data ini
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'attendance_time' => now(),
                ]
            );
        }

        return redirect()->route('agendas.show', $agenda->id)->with('success', 'Data absensi berhasil disimpan dan diperbarui.');
    }
}
