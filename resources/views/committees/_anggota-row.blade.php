<div class="flex items-center justify-between px-5 py-3">
    <div class="flex items-center gap-3">
        @if ($cm->member?->photo_url)
            <img src="{{ $cm->member->photo_url }}" alt="{{ $cm->member->name }}" class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">
        @else
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                {{ strtoupper(substr($cm->member->name ?? '?', 0, 1)) }}
            </div>
        @endif
        <div>
            <p class="text-sm font-semibold text-slate-800 dark:text-white">
                @if ($cm->member)
                    <a href="{{ route('members.statistik', $cm->member->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400">{{ $cm->member->name }}</a>
                @else
                    (orang telah dihapus)
                @endif
                @if ($cm->member && $cm->member->membership_type === 'Non-Pengurus')
                    <span class="ml-1.5 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">Non-Pengurus</span>
                @endif
            </p>
            <p class="text-xs text-slate-400">{{ $cm->position_category }}{{ $cm->notes ? ' · ' . $cm->notes : '' }}</p>
        </div>
    </div>
    @can('manage_committees')
        <form action="{{ route('kepanitiaan.anggota.destroy', [$cm->committee_id, $cm->id]) }}" method="POST" onsubmit="return confirm('Keluarkan orang ini dari kepanitiaan?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 dark:hover:bg-red-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </form>
    @endcan
</div>
