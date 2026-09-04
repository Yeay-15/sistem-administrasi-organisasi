<?php

namespace App\Http\Controllers;

use App\Models\EventUpdate;
use App\Models\FeaturedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventUpdateController extends Controller
{
    public function index(FeaturedEvent $event)
    {
        $updates = $event->updates()->paginate(15);

        return view('events.updates', compact('event', 'updates'));
    }

    public function store(Request $request, FeaturedEvent $event)
    {
        Gate::authorize('manage_events');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $event->updates()->create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'published_at' => now(),
        ]);

        return back()->with('success', 'Info terkini berhasil dipublikasikan.');
    }

    public function update(Request $request, FeaturedEvent $event, EventUpdate $update)
    {
        Gate::authorize('manage_events');
        abort_unless($update->featured_event_id === $event->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $update->update($validated);

        return back()->with('success', 'Info terkini berhasil diperbarui.');
    }

    public function destroy(FeaturedEvent $event, EventUpdate $update)
    {
        Gate::authorize('manage_events');
        abort_unless($update->featured_event_id === $event->id, 404);

        $update->delete();

        return back()->with('success', 'Info terkini berhasil dihapus.');
    }
}
