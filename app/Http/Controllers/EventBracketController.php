<?php

namespace App\Http\Controllers;

use App\Models\EventMatch;
use App\Models\EventTeam;
use App\Models\FeaturedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventBracketController extends Controller
{
    /**
     * Halaman "Kelola Bagan" — daftar tim yang sudah didaftarkan, form
     * tambah tim, tombol generate bagan, dan (jika bagan sudah ada)
     * tampilan bagan yang bisa diisi skornya langsung dari sini.
     */
    public function show(FeaturedEvent $event)
    {
        abort_unless($event->has_bracket, 404);

        $teams = $event->teams()->get();
        $bracketRounds = $event->bracketRounds();

        return view('events.bracket', compact('event', 'teams', 'bracketRounds'));
    }

    public function storeTeam(Request $request, FeaturedEvent $event)
    {
        abort_unless($event->has_bracket, 404);
        Gate::authorize('manage_events');

        $currentCount = $event->teams()->count();

        if ($currentCount >= $event->team_count) {
            return back()->with('error', 'Jumlah tim sudah mencapai batas (' . $event->team_count . ' tim). Hapus salah satu tim dulu jika ingin menggantinya.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        $data = [
            'featured_event_id' => $event->id,
            'name' => $validated['name'],
            // Nomor urut sekadar penanda tim di daftar — TIDAK menentukan
            // pasangan pertandingan (pairing babak pertama diisi manual
            // oleh admin sesuai hasil undian/keputusan federasi).
            'seed' => $currentCount + 1,
        ];

        if ($request->hasFile('logo')) {
            $filename = Str::uuid() . '.' . $request->file('logo')->getClientOriginalExtension();
            $data['logo_path'] = $request->file('logo')->storeAs('event_teams', $filename, 'public');
        }

        EventTeam::create($data);

        return back()->with('success', 'Tim "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function destroyTeam(FeaturedEvent $event, EventTeam $team)
    {
        Gate::authorize('manage_events');
        abort_unless($team->featured_event_id === $event->id, 404);

        if ($event->matches()->exists()) {
            return back()->with('error', 'Bagan sudah digenerate — hapus/reset bagan dulu sebelum mengubah daftar tim.');
        }

        if ($team->logo_path && Storage::disk('public')->exists($team->logo_path)) {
            Storage::disk('public')->delete($team->logo_path);
        }

        $team->delete();

        // Rapikan ulang nomor seed supaya tetap berurutan tanpa celah.
        $event->teams()->orderBy('seed')->get()->values()->each(function ($t, $i) {
            $t->update(['seed' => $i + 1]);
        });

        return back()->with('success', 'Tim "' . $team->name . '" berhasil dihapus.');
    }

    /**
     * Membuat seluruh struktur pertandingan (babak pertama sampai final)
     * dalam keadaan KOSONG — tanpa pasangan tim otomatis. Pemasangan
     * (siapa lawan siapa) untuk babak pertama diisi manual oleh admin lewat
     * halaman Kelola Bagan, karena biasanya sudah ditentukan langsung oleh
     * federasi/panitia penyelenggara turnamen, bukan diundi oleh sistem.
     */
    public function generate(FeaturedEvent $event)
    {
        Gate::authorize('manage_events');
        abort_unless($event->has_bracket, 404);

        $teamCount = $event->teams()->count();

        if ($teamCount !== $event->team_count) {
            return back()->with('error', 'Jumlah tim (' . $teamCount . ') belum sesuai target (' . $event->team_count . ' tim). Lengkapi dulu daftar tim sebelum membuat bagan.');
        }

        if ($event->matches()->exists()) {
            return back()->with('error', 'Bagan untuk event ini sudah pernah dibuat. Gunakan "Reset Bagan" dulu jika ingin membuat ulang dari awal.');
        }

        $totalRounds = $event->totalRounds();

        DB::transaction(function () use ($event, $teamCount, $totalRounds) {
            for ($round = 1; $round <= $totalRounds; $round++) {
                $matchesInRound = $teamCount / (2 ** $round);
                for ($i = 1; $i <= $matchesInRound; $i++) {
                    EventMatch::create([
                        'featured_event_id' => $event->id,
                        'round' => $round,
                        'round_order' => $i,
                    ]);
                }
            }
        });

        return redirect()->route('events.bracket', $event)->with('success', 'Bagan berhasil dibuat. Silakan atur pasangan pertandingan babak pertama sesuai hasil undian/keputusan federasi.');
    }

    public function reset(FeaturedEvent $event)
    {
        Gate::authorize('manage_events');

        $event->matches()->delete();

        return back()->with('success', 'Bagan berhasil direset. Susunan tim tetap tersimpan, silakan buat ulang.');
    }
}

