@php
    $editable = $editable ?? false;
    $team1Name = $match->team1?->name ?? 'TBD';
    $team2Name = $match->team2?->name ?? 'TBD';
@endphp

<div class="theme-transition rounded-xl border {{ $match->status === 'finished' ? 'border-emerald-200 dark:border-emerald-500/30' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50/60 p-3 dark:bg-slate-800/40">
    @if ($editable)
        <form action="{{ route('events.matches.update', $match) }}" method="POST" class="space-y-1.5">
            @csrf
            @method('PATCH')
    @else
        <div class="space-y-1.5">
    @endif

        @if ($match->scheduled_at || $match->venue)
            <p class="mb-1 text-center text-[11px] text-slate-400">
                {{ $match->scheduled_at?->translatedFormat('d M Y, H:i') }}
                @if ($match->scheduled_at && $match->venue) &bull; @endif
                {{ $match->venue }}
            </p>
        @endif

        <div class="flex items-center justify-between gap-2 rounded-lg px-2.5 py-1.5 {{ $match->winner_id && $match->winner_id === $match->team1_id ? 'bg-amber-100 dark:bg-amber-500/15' : 'bg-white dark:bg-slate-900' }}">
            <span class="truncate text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $team1Name }}</span>
            @if ($editable)
                <input type="number" name="team1_score" min="0" value="{{ old('team1_score', $match->team1_score) }}"
                    class="w-12 shrink-0 rounded border border-slate-200 bg-white px-1.5 py-1 text-center text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            @else
                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $match->team1_score ?? '-' }}</span>
            @endif
        </div>
        <div class="flex items-center justify-between gap-2 rounded-lg px-2.5 py-1.5 {{ $match->winner_id && $match->winner_id === $match->team2_id ? 'bg-amber-100 dark:bg-amber-500/15' : 'bg-white dark:bg-slate-900' }}">
            <span class="truncate text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $team2Name }}</span>
            @if ($editable)
                <input type="number" name="team2_score" min="0" value="{{ old('team2_score', $match->team2_score) }}"
                    class="w-12 shrink-0 rounded border border-slate-200 bg-white px-1.5 py-1 text-center text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            @else
                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $match->team2_score ?? '-' }}</span>
            @endif
        </div>

        @if ($editable)
            <div class="flex items-center gap-1.5 pt-1">
                <select name="status" class="flex-1 rounded border border-slate-200 bg-white px-1.5 py-1 text-[11px] focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="scheduled" {{ $match->status === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="ongoing" {{ $match->status === 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
                    <option value="finished" {{ $match->status === 'finished' ? 'selected' : '' }}>Selesai</option>
                </select>
                @if ($match->stage !== 'group')
                    <select name="winner_id" title="Menang manual (mis. WO) — kosongkan untuk otomatis dari skor"
                        class="flex-1 rounded border border-slate-200 bg-white px-1.5 py-1 text-[11px] focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Auto (skor)</option>
                        <option value="{{ $match->team1_id }}" {{ $match->winner_id === $match->team1_id ? 'selected' : '' }}>{{ $team1Name }} (WO)</option>
                        <option value="{{ $match->team2_id }}" {{ $match->winner_id === $match->team2_id ? 'selected' : '' }}>{{ $team2Name }} (WO)</option>
                    </select>
                @endif
            </div>
            <div class="flex items-center gap-1.5 pt-1">
                <button type="submit" class="flex-1 rounded-lg bg-navy-800 px-2 py-1.5 text-[11px] font-semibold text-white transition hover:bg-navy-900 dark:bg-navy-600 dark:hover:bg-navy-700">Simpan</button>
                <button type="button" onclick="document.getElementById('delete-match-{{ $match->id }}').submit()"
                    title="Hapus pertandingan" class="rounded-lg border border-red-200 px-2 py-1.5 text-red-500 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-400 dark:hover:bg-red-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @elseif ($match->status === 'finished' && $match->notes)
            <p class="pt-1 text-center text-[11px] italic text-slate-400">{{ $match->notes }}</p>
        @endif

    @if ($editable)
        </form>
        <form id="delete-match-{{ $match->id }}" action="{{ route('events.bracket.matches.destroy', [$match->featured_event_id, $match]) }}" method="POST"
            onsubmit="return confirm('Hapus pertandingan {{ $team1Name }} vs {{ $team2Name }}?');" class="hidden">
            @csrf @method('DELETE')
        </form>
    @else
        </div>
    @endif
</div>
