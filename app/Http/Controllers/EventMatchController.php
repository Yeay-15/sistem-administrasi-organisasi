<?php

namespace App\Http\Controllers;

use App\Models\EventMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventMatchController extends Controller
{
    /**
     * Dipanggil dari halaman Kelola Bagan. Menangani dua hal sekaligus:
     *
     * 1. Untuk pertandingan BABAK PERTAMA: admin memilih manual siapa
     *    lawan siapa (team1_id/team2_id) — pairing turnamen futsal
     *    biasanya sudah ditentukan federasi/panitia lewat undian resmi,
     *    jadi sistem tidak menebak-nebak pasangannya sendiri.
     * 2. Untuk semua babak: admin menginput skor. Begitu status diubah ke
     *    "Selesai" dan pemenang jelas, otomatis didorong ke pertandingan
     *    babak berikutnya lewat EventMatch::propagateWinner().
     */
    public function update(Request $request, EventMatch $match)
    {
        Gate::authorize('manage_events');

        $isFirstRound = $match->round === 1;

        $rules = [
            'team1_score' => ['nullable', 'integer', 'min:0'],
            'team2_score' => ['nullable', 'integer', 'min:0'],
            'winner_id' => ['nullable', 'integer'],
            'scheduled_at' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,ongoing,finished'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];

        if ($isFirstRound) {
            $teamIds = $match->featuredEvent->teams()->pluck('id');
            $rules['team1_id'] = ['nullable', 'integer', 'in:' . $teamIds->implode(',')];
            $rules['team2_id'] = ['nullable', 'integer', 'in:' . $teamIds->implode(',')];
        }

        $validated = $request->validate($rules);

        $data = [
            'team1_score' => $validated['team1_score'] ?? null,
            'team2_score' => $validated['team2_score'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($isFirstRound) {
            $team1Id = $validated['team1_id'] ?? null;
            $team2Id = $validated['team2_id'] ?? null;

            if ($team1Id && $team2Id && $team1Id === $team2Id) {
                return back()->with('error', 'Tim 1 dan Tim 2 tidak boleh sama.');
            }

            // Pastikan satu tim tidak dipasang ganda di dua pertandingan
            // babak pertama yang berbeda pada event yang sama.
            $alreadyUsed = EventMatch::where('featured_event_id', $match->featured_event_id)
                ->where('round', 1)
                ->where('id', '!=', $match->id)
                ->where(function ($q) use ($team1Id, $team2Id) {
                    $q->whereIn('team1_id', array_filter([$team1Id, $team2Id]))
                        ->orWhereIn('team2_id', array_filter([$team1Id, $team2Id]));
                })
                ->exists();

            if ($alreadyUsed) {
                return back()->with('error', 'Salah satu tim sudah dipasangkan di pertandingan babak pertama lain. Periksa kembali susunan bagan.');
            }

            $data['team1_id'] = $team1Id;
            $data['team2_id'] = $team2Id;
        }

        // Pemenang ditentukan otomatis dari skor yang lebih tinggi, KECUALI
        // admin memilih pemenang secara manual (mis. kasus walk-over / WO
        // di mana skor tidak relevan) lewat dropdown "Menang Manual".
        $team1Id = $data['team1_id'] ?? $match->team1_id;
        $team2Id = $data['team2_id'] ?? $match->team2_id;
        $winnerId = in_array($validated['winner_id'] ?? null, [$team1Id, $team2Id]) ? $validated['winner_id'] : null;

        if (! $winnerId && $data['status'] === 'finished'
            && $data['team1_score'] !== null && $data['team2_score'] !== null
            && $data['team1_score'] !== $data['team2_score']) {
            $winnerId = $data['team1_score'] > $data['team2_score'] ? $team1Id : $team2Id;
        }

        $data['winner_id'] = $data['status'] === 'finished' ? $winnerId : null;

        $match->update($data);

        if ($data['status'] === 'finished' && $data['winner_id']) {
            $match->propagateWinner();
        }

        return back()->with('success', 'Pertandingan berhasil disimpan.');
    }
}
