<?php

namespace App\Http\Controllers;

use App\Models\FeaturedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = FeaturedEvent::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->withCount('teams')->latest('event_start_date')->paginate(10)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $data = [
            'title' => $validated['title'],
            'slug' => $this->generateUniqueSlug($validated['title']),
            'short_description' => $validated['short_description'] ?? null,
            'content' => $validated['content'] ?? null,
            'location' => $validated['location'] ?? null,
            'event_start_date' => $validated['event_start_date'],
            'event_end_date' => $validated['event_end_date'] ?? null,
            'registration_url' => $validated['registration_url'] ?? null,
            'cta_label' => $validated['cta_label'] ?? 'Daftar Sekarang',
            'has_bracket' => $request->boolean('has_bracket'),
            'status' => $validated['status'],
            'show_on_homepage' => $request->boolean('show_on_homepage'),
            'show_announcement_bar' => $request->boolean('show_announcement_bar'),
        ];

        if ($request->hasFile('poster')) {
            $data['poster_path'] = $this->storePoster($request);
        }

        $event = FeaturedEvent::create($data);

        return redirect()->route('events.index')
            ->with('success', 'Event "' . $event->title . '" berhasil dibuat.' . ($event->has_bracket
                ? ' Lanjutkan dengan menambahkan tim &amp; grup di menu "Kelola Bagan".'
                : ''));
    }

    public function edit(FeaturedEvent $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, FeaturedEvent $event)
    {
        $validated = $this->validateRequest($request, $event->id);

        $data = [
            'title' => $validated['title'],
            'short_description' => $validated['short_description'] ?? null,
            'content' => $validated['content'] ?? null,
            'location' => $validated['location'] ?? null,
            'event_start_date' => $validated['event_start_date'],
            'event_end_date' => $validated['event_end_date'] ?? null,
            'registration_url' => $validated['registration_url'] ?? null,
            'cta_label' => $validated['cta_label'] ?? 'Daftar Sekarang',
            'has_bracket' => $request->boolean('has_bracket'),
            'status' => $validated['status'],
            'show_on_homepage' => $request->boolean('show_on_homepage'),
            'show_announcement_bar' => $request->boolean('show_announcement_bar'),
        ];

        if ($validated['title'] !== $event->title) {
            $data['slug'] = $this->generateUniqueSlug($validated['title'], $event->id);
        }

        if ($request->hasFile('poster')) {
            $this->deletePoster($event);
            $data['poster_path'] = $this->storePoster($request);
        } elseif ($request->boolean('remove_poster')) {
            $this->deletePoster($event);
            $data['poster_path'] = null;
        }

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(FeaturedEvent $event)
    {
        Gate::authorize('delete_events');

        $this->deletePoster($event);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Switch cepat "tampil di beranda" dari daftar event — tanpa perlu
     * masuk ke form edit. Meniru pola AgendaController::togglePublic.
     */
    public function toggleHomepage(FeaturedEvent $event)
    {
        Gate::authorize('manage_events');

        $event->update(['show_on_homepage' => ! $event->show_on_homepage]);

        return back()->with('success', $event->show_on_homepage
            ? 'Event "' . $event->title . '" sekarang tampil di beranda.'
            : 'Highlight beranda untuk "' . $event->title . '" disembunyikan.');
    }

    /**
     * Switch cepat pita pengumuman (tampil di semua halaman publik).
     */
    public function toggleAnnouncement(FeaturedEvent $event)
    {
        Gate::authorize('manage_events');

        $event->update(['show_announcement_bar' => ! $event->show_announcement_bar]);

        return back()->with('success', $event->show_announcement_bar
            ? 'Pita pengumuman untuk "' . $event->title . '" diaktifkan.'
            : 'Pita pengumuman untuk "' . $event->title . '" dimatikan.');
    }

    private function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_start_date' => ['required', 'date'],
            'event_end_date' => ['nullable', 'date', 'after_or_equal:event_start_date'],
            'registration_url' => ['nullable', 'url', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:50'],
            'has_bracket' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,active,archived'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_poster' => ['nullable', 'boolean'],
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 2;

        while (
            FeaturedEvent::withTrashed()->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function storePoster(Request $request): string
    {
        $filename = Str::uuid() . '.' . $request->file('poster')->getClientOriginalExtension();
        return $request->file('poster')->storeAs('events', $filename, 'public');
    }

    private function deletePoster(FeaturedEvent $event): void
    {
        if ($event->poster_path && Storage::disk('public')->exists($event->poster_path)) {
            Storage::disk('public')->delete($event->poster_path);
        }
    }
}
