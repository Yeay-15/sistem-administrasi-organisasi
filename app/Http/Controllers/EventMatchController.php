<?php

namespace App\Http\Controllers;

use App\Models\EventMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventMatchController extends Controller
{
    /**
     * Update skor/status satu pertandingan yang sudah dibuat (lawan
     * tandingnya sendiri ditentukan sekali di awal lewat
     * EventBracketController::storeMatch dan tidak diubah lagi di sini —
     * kalau salah pasang, hapus pertandingannya lalu buat ulang).
     */
    public function update(Request $request, EventMatch $match)
    {
        Gate::authorize('manage_events');

        $validated = $request->validate([
            'team1_score' => ['nullable', 'integer', 'min:0'],
            'team2_score' => ['nullable', 'integer', 'min:0'],
            'winner_id' => ['nullable', 'integer', 'in:' . implode(',', array_filter([$match->team1_id, $match->team2_id]))],
            'scheduled_at' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,ongoing,finished'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        // Pemenang ditentukan otomatis dari skor yang lebih tinggi, KECUALI
        // admin memilih pemenang secara manual (mis. kasus walk-over / WO
        // di mana skor tidak relevan) lewat dropdown "Menang Manual". Untuk
        // pertandingan fase grup, "pemenang" umumnya tidak relevan (bisa
        // seri) — kolom ini tetap boleh kosong, klasemen dihitung dari skor.
        $winnerId = $validated['winner_id'] ?? null;

        if (! $winnerId && $validated['status'] === 'finished'
            && $validated['team1_score'] !== null && $validated['team2_score'] !== null
            && $validated['team1_score'] !== $validated['team2_score']) {
            $winnerId = $validated['team1_score'] > $validated['team2_score'] ? $match->team1_id : $match->team2_id;
        }

        $match->update([
            'team1_score' => $validated['team1_score'],
            'team2_score' => $validated['team2_score'],
            'winner_id' => $validated['status'] === 'finished' ? $winnerId : null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Hasil pertandingan berhasil disimpan.');
    }
}
