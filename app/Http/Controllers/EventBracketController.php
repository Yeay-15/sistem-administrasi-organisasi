<?php

namespace App\Http\Controllers;

use App\Models\EventGroup;
use App\Models\EventMatch;
use App\Models\EventTeam;
use App\Models\FeaturedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventBracketController extends Controller
{
    /**
     * Halaman "Kelola Bagan" — kelola grup, daftar tim (+ penempatan ke
     * grup), klasemen otomatis per grup, dan daftar pertandingan (fase
     * grup maupun babak gugur) yang seluruhnya diinput manual oleh admin.
     * Tidak ada jumlah tim baku (8/16/32/64) — jumlah tim & grup mengikuti
     * berapa yang benar-benar mendaftar & mekanisme dari federasi.
     */
    public function show(FeaturedEvent $event)
    {
        abort_unless($event->has_bracket, 404);

        $groups = $event->groups()->withCount('teams')->get();
        $groupsWithStandings = $event->groupsWithStandings();
        $knockoutStages = $event->knockoutMatchesByStage();
        $allTeams = $event->teams;

        return view('events.bracket', compact(
            'event', 'groups', 'groupsWithStandings', 'knockoutStages', 'allTeams'
        ));
    }

    // ==================== TIM ====================

    public function storeTeam(Request $request, FeaturedEvent $event)
    {
        abort_unless($event->has_bracket, 404);
        Gate::authorize('manage_events');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'event_group_id' => ['nullable', 'integer', 'exists:event_groups,id'],
        ]);

        $data = [
            'featured_event_id' => $event->id,
            'name' => $validated['name'],
            'event_group_id' => $validated['event_group_id'] ?? null,
            'seed' => $event->teams()->count() + 1,
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

        if ($team->logo_path && Storage::disk('public')->exists($team->logo_path)) {
            Storage::disk('public')->delete($team->logo_path);
        }

        $team->delete();

        return back()->with('success', 'Tim "' . $team->name . '" berhasil dihapus.');
    }

    /**
     * Pindahkan satu tim ke grup tertentu (atau lepas dari grup lewat
     * opsi "Belum ada grup") — dipanggil dari dropdown per baris tim.
     */
    public function assignTeamGroup(Request $request, FeaturedEvent $event, EventTeam $team)
    {
        Gate::authorize('manage_events');
        abort_unless($team->featured_event_id === $event->id, 404);

        $validated = $request->validate([
            'event_group_id' => ['nullable', 'integer', 'exists:event_groups,id'],
        ]);

        $team->update(['event_group_id' => $validated['event_group_id'] ?? null]);

        return back()->with('success', 'Grup untuk tim "' . $team->name . '" berhasil diperbarui.');
    }

    // ==================== GRUP ====================

    public function storeGroup(Request $request, FeaturedEvent $event)
    {
        abort_unless($event->has_bracket, 404);
        Gate::authorize('manage_events');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $event->groups()->create([
            'name' => $validated['name'],
            'order' => $event->groups()->count() + 1,
        ]);

        return back()->with('success', 'Grup "' . $validated['name'] . '" berhasil dibuat.');
    }

    public function destroyGroup(FeaturedEvent $event, EventGroup $group)
    {
        Gate::authorize('manage_events');
        abort_unless($group->featured_event_id === $event->id, 404);

        // Tim di grup ini otomatis kembali ke status "belum ada grup"
        // (FK nullOnDelete), pertandingan grup ini ikut terhapus.
        $group->delete();

        return back()->with('success', 'Grup "' . $group->name . '" berhasil dihapus.');
    }

    // ==================== PERTANDINGAN ====================

    /**
     * Tambah satu pertandingan secara manual — untuk fase grup maupun
     * babak gugur. Admin memilih sendiri lawan tandingnya (sesuai hasil
     * undian resmi dari federasi), sistem tidak menebak pasangannya.
     */
    public function storeMatch(Request $request, FeaturedEvent $event)
    {
        abort_unless($event->has_bracket, 404);
        Gate::authorize('manage_events');

        $validated = $request->validate([
            'stage' => ['required', 'in:' . implode(',', array_keys(EventMatch::STAGES))],
            'event_group_id' => ['required_if:stage,group', 'nullable', 'integer', 'exists:event_groups,id'],
            'team1_id' => ['required', 'integer', 'different:team2_id', 'exists:event_teams,id'],
            'team2_id' => ['required', 'integer', 'exists:event_teams,id'],
            'scheduled_at' => ['nullable', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
        ]);

        $teamIds = $event->teams()->pluck('id');
        if (! $teamIds->contains((int) $validated['team1_id']) || ! $teamIds->contains((int) $validated['team2_id'])) {
            return back()->with('error', 'Tim yang dipilih tidak terdaftar di event ini.');
        }

        $groupId = $validated['stage'] === 'group' ? $validated['event_group_id'] : null;

        if ($groupId) {
            $groupTeamIds = EventTeam::where('event_group_id', $groupId)->pluck('id');
            if (! $groupTeamIds->contains((int) $validated['team1_id']) || ! $groupTeamIds->contains((int) $validated['team2_id'])) {
                return back()->with('error', 'Kedua tim harus berada di grup yang sama untuk pertandingan fase grup.');
            }
        }

        $nextOrder = EventMatch::where('featured_event_id', $event->id)
            ->where('stage', $validated['stage'])
            ->when($groupId, fn ($q) => $q->where('event_group_id', $groupId))
            ->max('round_order') + 1;

        EventMatch::create([
            'featured_event_id' => $event->id,
            'stage' => $validated['stage'],
            'event_group_id' => $groupId,
            'round_order' => $nextOrder,
            'team1_id' => $validated['team1_id'],
            'team2_id' => $validated['team2_id'],
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'venue' => $validated['venue'] ?? null,
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Pertandingan berhasil ditambahkan.');
    }

    public function destroyMatch(FeaturedEvent $event, EventMatch $match)
    {
        Gate::authorize('manage_events');
        abort_unless($match->featured_event_id === $event->id, 404);

        $match->delete();

        return back()->with('success', 'Pertandingan berhasil dihapus.');
    }
}
