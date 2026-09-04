@extends('layouts.app')

@section('title', 'Kelola Bagan - ' . $event->title)

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('events.index') }}"
            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Kelola Bagan &amp; Tim</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $event->title }} &bull; {{ $event->team_count }} Tim</p>
        </div>
    </div>

    @if ($bracketRounds->isEmpty())
        {{-- ===== BELUM DIGENERATE: kelola daftar tim dulu ===== --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300">Daftar Tim Peserta</h2>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            {{ $teams->count() }} / {{ $event->team_count }}
                        </span>
                    </div>

                    <div class="max-h-[420px] space-y-2 overflow-y-auto pr-1">
                        @forelse ($teams as $team)
                            <div class="flex items-center gap-3 rounded-lg border border-slate-100 px-3 py-2 dark:border-slate-800">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ $team->seed }}</span>
                                @if ($team->logo_path)
                                    <img src="{{ asset('storage/' . $team->logo_path) }}" class="h-6 w-6 rounded-full object-cover" alt="">
                                @endif
                                <span class="flex-1 text-sm text-slate-700 dark:text-slate-200">{{ $team->name }}</span>
                                <form action="{{ route('events.bracket.teams.destroy', [$event, $team]) }}" method="POST"
                                    onsubmit="return confirm('Hapus tim {{ $team->name }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded p-1 text-slate-300 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-slate-400">Belum ada tim ditambahkan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                @if ($teams->count() < $event->team_count)
                    <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h2 class="mb-3 text-sm font-bold text-slate-700 dark:text-slate-300">Tambah Tim</h2>
                        <form action="{{ route('events.bracket.teams.store', $event) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="text" name="name" required placeholder="Nama sekolah / tim"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                            <input type="file" name="logo" accept="image/*"
                                class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Tambah Tim</button>
                        </form>
                        <p class="mt-2 text-xs text-slate-400">Tambahkan seluruh peserta di sini dulu. Pasangan siapa lawan siapa di babak pertama diatur belakangan di halaman bagan (setelah tombol "Buat Bagan" ditekan).</p>
                    </div>
                @endif

                <div class="theme-transition rounded-2xl border border-navy-100 bg-navy-50/60 p-5 dark:border-navy-500/20 dark:bg-navy-500/10">
                    <h2 class="mb-1.5 text-sm font-bold text-navy-800 dark:text-navy-300">Buat Bagan</h2>
                    <p class="mb-3 text-xs text-navy-700/80 dark:text-navy-300/70">Bagan akan dibuat dalam keadaan kosong begitu jumlah tim mencapai {{ $event->team_count }} — pasangan pertandingan babak pertama diisi manual sesuai hasil undian/keputusan federasi.</p>
                    <form action="{{ route('events.bracket.generate', $event) }}" method="POST"
                        onsubmit="return confirm('Buat bagan sekarang?');">
                        @csrf
                        <button type="submit" {{ $teams->count() === $event->team_count ? '' : 'disabled' }}
                            class="w-full rounded-lg bg-navy-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-navy-800 disabled:cursor-not-allowed disabled:opacity-40">
                            Buat Bagan ({{ $teams->count() }}/{{ $event->team_count }})
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- ===== BAGAN SUDAH DIGENERATE ===== --}}
        <div class="mb-4 flex justify-end">
            <form action="{{ route('events.bracket.reset', $event) }}" method="POST"
                onsubmit="return confirm('Reset bagan? Semua hasil pertandingan yang sudah diinput akan hilang (susunan tim tetap tersimpan).');">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-400 dark:hover:bg-red-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reset Bagan
                </button>
            </form>
        </div>

        <div class="theme-transition overflow-x-auto rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex min-w-max items-start gap-6">
                @foreach ($bracketRounds as $roundData)
                    <div class="w-64 shrink-0">
                        <h3 class="mb-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $roundData['label'] }}</h3>
                        <div class="flex h-full flex-col justify-around gap-6">
                            @foreach ($roundData['matches'] as $match)
                                @include('events._match-card', ['match' => $match, 'editable' => true, 'teams' => $teams])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
