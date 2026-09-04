@php
    $loopIndex = $loopIndex ?? 0;
@endphp
<a href="{{ route('public.events.show', $event) }}" class="reveal group block" style="--reveal-delay: {{ ($loopIndex % 6) * 70 }}ms">
    <div class="theme-transition h-full overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
        <div class="relative aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
            @if ($event->poster_url)
                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" loading="lazy"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
            @else
                <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                    </svg>
                </div>
            @endif
            @if ($event->status === 'archived')
                <span class="absolute right-2.5 top-2.5 rounded-full bg-slate-800/80 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">Arsip</span>
            @else
                <span class="absolute right-2.5 top-2.5 rounded-full bg-emerald-500 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">Aktif</span>
            @endif
        </div>
        <div class="p-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">{{ $event->title }}</h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                {{ $event->event_start_date->translatedFormat('d M Y') }}
                @if ($event->location) &bull; {{ $event->location }} @endif
            </p>
            @if ($event->short_description)
                <p class="mt-2 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $event->short_description }}</p>
            @endif
        </div>
    </div>
</a>
