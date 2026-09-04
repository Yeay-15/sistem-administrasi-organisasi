@php
    $editable = $editable ?? false;
    $isFirstRound = $match->round === 1;
    $showTeamSelect = $editable && $isFirstRound;
    $teams = $teams ?? collect();
    $team1Name = $match->team1?->name ?? 'Menunggu';
    $team2Name = $match->team2?->name ?? 'Menunggu';
@endphp

<div class="theme-transition rounded-xl border {{ $match->status === 'finished' ? 'border-emerald-200 dark:border-emerald-500/30' : 'border-slate-200 dark:border-slate-700' }} bg-slate-50/60 dark:bg-slate-800/40">
    @if ($editable)
        <form action="{{ route('events.matches.update', $match) }}" method="POST" class="p-3">
            @csrf
            @method('PATCH')
    @else
        <div class="p-3">
    @endif

        <div class="space-y-1.5">
            <div class="flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 {{ $match->winner_id && $match->winner_id === $match->team1_id ? 'bg-amber-100 dark:bg-amber-500/15' : 'bg-white dark:bg-slate-900' }}">
                @if ($showTeamSelect)
                    <select name="team1_id" class="min-w-0 flex-1 truncate rounded border border-slate-200 bg-white px-1.5 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Pilih Tim 1 --</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}" {{ $match->team1_id === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                @else
                    <span class="truncate text-xs font-semibold {{ $match->team1_id ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400 italic dark:text-slate-500' }}">{{ $team1Name }}</span>
                @endif
                @if ($editable)
                    <input type="number" name="team1_score" min="0" value="{{ old('team1_score', $match->team1_score) }}" {{ $match->team1_id ? '' : 'disabled' }}
                        class="w-12 shrink-0 rounded border border-slate-200 bg-white px-1.5 py-1 text-center text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 disabled:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:disabled:bg-slate-800/50">
                @else
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $match->team1_score ?? '-' }}</span>
                @endif
            </div>
            <div class="flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 {{ $match->winner_id && $match->winner_id === $match->team2_id ? 'bg-amber-100 dark:bg-amber-500/15' : 'bg-white dark:bg-slate-900' }}">
                @if ($showTeamSelect)
                    <select name="team2_id" class="min-w-0 flex-1 truncate rounded border border-slate-200 bg-white px-1.5 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Pilih Tim 2 --</option>
                        @foreach ($teams as $t)
                            <option value="{{ $t->id }}" {{ $match->team2_id === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                @else
                    <span class="truncate text-xs font-semibold {{ $match->team2_id ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400 italic dark:text-slate-500' }}">{{ $team2Name }}</span>
                @endif
                @if ($editable)
                    <input type="number" name="team2_score" min="0" value="{{ old('team2_score', $match->team2_score) }}" {{ $match->team2_id ? '' : 'disabled' }}
                        class="w-12 shrink-0 rounded border border-slate-200 bg-white px-1.5 py-1 text-center text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 disabled:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:disabled:bg-slate-800/50">
                @else
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $match->team2_score ?? '-' }}</span>
                @endif
            </div>
        </div>

        @if ($editable && ($match->team1_id || $showTeamSelect) && ($match->team2_id || $showTeamSelect))
            <div class="mt-2 flex items-center gap-1.5">
                <select name="status" class="flex-1 rounded border border-slate-200 bg-white px-1.5 py-1 text-[11px] focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="scheduled" {{ $match->status === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="ongoing" {{ $match->status === 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
                    <option value="finished" {{ $match->status === 'finished' ? 'selected' : '' }}>Selesai</option>
                </select>
                @if ($match->team1_id && $match->team2_id)
                    <select name="winner_id" title="Menang manual (mis. WO) — kosongkan untuk otomatis dari skor"
                        class="flex-1 rounded border border-slate-200 bg-white px-1.5 py-1 text-[11px] focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Auto (skor)</option>
                        <option value="{{ $match->team1_id }}" {{ $match->winner_id === $match->team1_id ? 'selected' : '' }}>{{ $team1Name }} (WO)</option>
                        <option value="{{ $match->team2_id }}" {{ $match->winner_id === $match->team2_id ? 'selected' : '' }}>{{ $team2Name }} (WO)</option>
                    </select>
                @endif
            </div>
            <button type="submit" class="mt-2 w-full rounded-lg bg-navy-800 px-2 py-1.5 text-[11px] font-semibold text-white transition hover:bg-navy-900 dark:bg-navy-600 dark:hover:bg-navy-700">Simpan</button>
        @elseif ($match->status === 'finished' && $match->notes)
            <p class="mt-1.5 text-center text-[11px] italic text-slate-400">{{ $match->notes }}</p>
        @endif

    @if ($editable)
        </form>
    @else
        </div>
    @endif
</div>
