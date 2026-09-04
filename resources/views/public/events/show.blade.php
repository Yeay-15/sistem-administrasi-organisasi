@extends('layouts.public')

@section('title', $event->title . ' - KATIBER')
@section('meta_description', $event->short_description ?? Str::limit(strip_tags($event->content ?? ''), 150))

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden border-t-4 border-amber-400 bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950">
        <div class="navy-dot-pattern absolute inset-0"></div>
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-amber-400/10 blur-3xl"></div>
        <div class="relative mx-auto flex max-w-5xl flex-col items-center gap-8 px-4 py-16 sm:px-6 lg:flex-row lg:px-8">
            @if ($event->poster_url)
                <img src="{{ $event->poster_url }}" alt="Poster {{ $event->title }}"
                    class="reveal h-64 w-auto shrink-0 rounded-xl object-cover shadow-2xl ring-4 ring-amber-400/30">
            @endif
            <div class="reveal text-center lg:text-left">
                @if ($event->status === 'archived')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-white ring-1 ring-inset ring-white/15">Acara Telah Selesai</span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-navy-950">🏆 Sedang Berlangsung</span>
                @endif
                <h1 class="mt-4 text-3xl font-extrabold leading-tight text-white sm:text-4xl">{{ $event->title }}</h1>
                @if ($event->short_description)
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-navy-100 sm:text-base lg:mx-0">{{ $event->short_description }}</p>
                @endif
                <p class="mt-3 flex flex-wrap items-center justify-center gap-x-1.5 gap-y-1 text-sm font-semibold text-navy-100 lg:justify-start">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    {{ $event->event_start_date->translatedFormat('d F Y') }}
                    @if ($event->event_end_date && ! $event->event_end_date->isSameDay($event->event_start_date))
                        &ndash; {{ $event->event_end_date->translatedFormat('d F Y') }}
                    @endif
                    @if ($event->location)<span class="mx-1">&bull;</span> {{ $event->location }}@endif
                </p>
                @if ($event->status === 'active' && $event->registration_url)
                    <div class="mt-6">
                        <a href="{{ $event->registration_url }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-400 px-6 py-3 text-sm font-bold text-navy-950 shadow-lg shadow-amber-400/20 transition hover:-translate-y-0.5 hover:bg-amber-300 hover:shadow-xl hover:shadow-amber-400/30">
                            {{ $event->cta_label }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
        {{-- ============ ISI / SYARAT & KETENTUAN ============ --}}
        @if ($event->content)
            <div class="reveal prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 dark:prose-invert dark:text-slate-300 sm:text-base">
                {!! $event->content !!}
            </div>
        @endif

        {{-- ============ INFO TERKINI ============
             Gaya timeline mengikuti pola "Estafet Kepemimpinan" di halaman
             Profil: garis vertikal gradasi navy, ikon bulat ber-ring putih,
             kartu putih, dan badge navy — item terbaru ditandai gold. --}}
        @if ($updates->isNotEmpty())
            <div class="reveal mt-14">
                <h2 class="mb-5 text-lg font-bold text-slate-800 dark:text-white">📣 Info Terkini</h2>
                <div class="relative">
                    <div class="absolute bottom-2 left-[15px] top-2 w-0.5 bg-gradient-to-b from-navy-700 via-navy-300 to-navy-50 dark:from-navy-500 dark:via-navy-800 dark:to-slate-900"></div>
                    <div class="space-y-5">
                        @foreach ($updates as $update)
                            <div class="relative flex items-start gap-4">
                                <span class="relative z-10 mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-navy-700 text-white shadow-md ring-4 ring-white dark:bg-navy-600 dark:ring-slate-950">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                                    </svg>
                                </span>
                                <div class="theme-transition flex-1 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900 {{ $loop->first ? 'ring-1 ring-amber-300/60 dark:ring-amber-400/20' : '' }}">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-block rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-bold text-navy-700 dark:bg-navy-500/10 dark:text-navy-400">
                                            {{ $update->published_at?->translatedFormat('d M Y, H:i') }}
                                        </span>
                                        @if ($loop->first)
                                            <span class="inline-block rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">Terbaru</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-2 text-sm font-bold text-slate-800 dark:text-white">{{ $update->title }}</h3>
                                    <p class="mt-1 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $update->body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ============ BAGAN TURNAMEN ============ --}}
        @if ($event->has_bracket && $bracketRounds->isNotEmpty())
            <div class="reveal mt-14">
                <h2 class="mb-5 text-lg font-bold text-slate-800 dark:text-white">🏆 Bagan Pertandingan</h2>
                <div class="theme-transition overflow-x-auto rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex min-w-max items-start gap-6">
                        @foreach ($bracketRounds as $roundData)
                            <div class="w-60 shrink-0">
                                <h3 class="mb-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $roundData['label'] }}</h3>
                                <div class="flex h-full flex-col justify-around gap-6">
                                    @foreach ($roundData['matches'] as $match)
                                        @include('events._match-card', ['match' => $match, 'editable' => false])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="reveal mt-14 text-center">
            <a href="{{ route('public.events.index') }}" class="text-sm font-semibold text-navy-700 hover:underline dark:text-navy-300">&larr; Lihat semua Event Unggulan</a>
        </div>
    </div>
@endsection
