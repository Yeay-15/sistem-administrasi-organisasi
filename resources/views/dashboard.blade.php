@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $hour = \Carbon\Carbon::now()->hour;
        $greeting =
            $hour < 11
                ? 'Selamat pagi'
                : ($hour < 15
                    ? 'Selamat siang'
                    : ($hour < 19
                        ? 'Selamat sore'
                        : 'Selamat malam'));
        $firstName = explode(' ', trim(Auth::user()->name ?? 'Admin'))[0];
    @endphp

    <div x-data="{ statView: 'pengurus' }">

        {{-- Hero / sapaan --}}
        <div
            class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-600 to-indigo-700 px-6 py-7 text-white shadow-sm sm:px-8">
            <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-white/10"></div>
            <div class="relative flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-medium text-blue-100">{{ $greeting }},</p>
                    <h1 class="mt-1 text-2xl font-bold sm:text-3xl">{{ $firstName }} 👋</h1>
                    <p class="mt-2 max-w-md text-sm text-blue-100">
                        Berikut ringkasan aktivitas Sistem KATIBER untuk bulan
                        {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}.
                    </p>
                </div>
                <div class="flex flex-col items-start gap-3 sm:items-end">
                    <div
                        class="inline-flex items-center gap-2 self-start rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur sm:self-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>

                    {{-- Toggle Statistik Kepengurusan / Website --}}
                    <div class="flex w-fit rounded-lg bg-white/10 p-1 backdrop-blur">
                        <button @click="statView = 'pengurus'" type="button"
                            class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold transition"
                            :class="statView === 'pengurus' ? 'bg-white text-blue-700 shadow-sm' :
                                'text-blue-100 hover:text-white'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.106A12.318 12.318 0 008.624 15c-2.331 0-4.512.645-6.374 1.766L2.25 17c0 1.6.5 3.086 1.352 4.31M15 19.128V21m-6.75-3.235A4.125 4.125 0 0112 14.25a4.125 4.125 0 013.75 3.515M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Kepengurusan
                        </button>
                        <button @click="statView = 'website'" type="button"
                            class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold transition"
                            :class="statView === 'website' ? 'bg-white text-blue-700 shadow-sm' :
                                'text-blue-100 hover:text-white'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-3.5 w-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m-15.432 0A8.959 8.959 0 013 12c0-.778.099-1.533.284-2.253" />
                            </svg>
                            Website
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ===================== STATISTIK KEPENGURUSAN ================ --}}
        {{-- ============================================================ --}}
        <div x-show="statView === 'pengurus'" x-cloak>

            {{-- KPI cards --}}
            <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pengurus Aktif</p>
                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">{{ $kpi['total_members'] }}
                            </p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-5.5 w-5.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.106A12.318 12.318 0 008.624 15c-2.331 0-4.512.645-6.374 1.766L2.25 17c0 1.6.5 3.086 1.352 4.31M15 19.128V21m-6.75-3.235A4.125 4.125 0 0112 14.25a4.125 4.125 0 013.75 3.515M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Agenda Bulan Ini</p>
                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">{{ $kpi['total_agendas'] }}
                            </p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-5.5 w-5.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Surat Masuk (Bulan Ini)</p>
                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ $kpi['total_incoming'] }}</p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-5.5 w-5.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3v13.5m0 0l-4.5-4.5M12 16.5l4.5-4.5M3.75 19.5h16.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Surat Keluar (Bulan Ini)</p>
                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ $kpi['total_outgoing'] }}</p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-5.5 w-5.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12L3.269 3.126A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart & aktivitas terkini --}}
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

                {{-- Komposisi absensi --}}
                <div
                    class="theme-transition col-span-1 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3
                        class="mb-4 border-b border-slate-100 pb-3 text-base font-bold text-slate-800 dark:border-slate-800 dark:text-white">
                        Komposisi Absensi
                    </h3>
                    <div class="relative flex h-64 w-full items-center justify-center">
                        @if (array_sum($attendanceStats) > 0)
                            <canvas id="attendanceChart"></canvas>
                        @else
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="mx-auto mb-2 h-10 w-10 text-slate-300 dark:text-slate-700">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                                </svg>
                                <p class="text-sm italic text-slate-400 dark:text-slate-500">Belum ada data absensi
                                    untuk
                                    ditampilkan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Aktivitas surat --}}
                <div
                    class="theme-transition col-span-1 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm xl:col-span-2 dark:border-slate-800 dark:bg-slate-900">
                    <h3
                        class="mb-4 border-b border-slate-100 pb-3 text-base font-bold text-slate-800 dark:border-slate-800 dark:text-white">
                        Aktivitas Surat Terkini
                    </h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Surat masuk --}}
                        <div>
                            <h4 class="mb-3 flex items-center text-sm font-semibold text-blue-600 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.75" stroke="currentColor" class="mr-2 h-4.5 w-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                Surat Masuk Terbaru
                            </h4>
                            <ul class="space-y-2.5">
                                @forelse ($recentIncoming as $in)
                                    <li
                                        class="theme-transition rounded-lg border border-slate-100 bg-slate-50 p-3 text-sm dark:border-slate-800 dark:bg-slate-800/60">
                                        <span
                                            class="block text-xs text-slate-400 dark:text-slate-500">{{ \Carbon\Carbon::parse($in->received_date)->format('d M Y') }}</span>
                                        <span
                                            class="font-semibold text-slate-800 dark:text-slate-100">{{ $in->sender }}</span>
                                        <span class="block truncate text-slate-500 dark:text-slate-400"
                                            title="{{ $in->subject }}">{{ $in->subject }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm italic text-slate-400 dark:text-slate-500">Belum ada surat
                                        masuk.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Surat keluar --}}
                        <div>
                            <h4 class="mb-3 flex items-center text-sm font-semibold text-purple-600 dark:text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.75" stroke="currentColor" class="mr-2 h-4.5 w-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                Surat Keluar Terbaru
                            </h4>
                            <ul class="space-y-2.5">
                                @forelse ($recentOutgoing as $out)
                                    <li
                                        class="theme-transition rounded-lg border border-slate-100 bg-slate-50 p-3 text-sm dark:border-slate-800 dark:bg-slate-800/60">
                                        <span class="block text-xs text-slate-400 dark:text-slate-500">
                                            {{ \Carbon\Carbon::parse($out->date)->format('d M Y') }} •
                                            <span
                                                class="font-bold {{ $out->status == 'Terkirim' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $out->status }}</span>
                                        </span>
                                        <span
                                            class="font-semibold text-slate-800 dark:text-slate-100">{{ $out->destination }}</span>
                                        <span class="block truncate text-slate-500 dark:text-slate-400"
                                            title="{{ $out->subject }}">{{ $out->subject }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm italic text-slate-400 dark:text-slate-500">Belum ada surat
                                        keluar.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Widget Tambahan: Agenda Terdekat & Radar Pembinaan --}}
            <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">

                {{-- Widget 1: Agenda Terdekat --}}
                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <h3 class="flex items-center text-base font-bold text-slate-800 dark:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="mr-2 h-5 w-5 text-emerald-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            Agenda Terdekat
                        </h3>
                        <a href="{{ route('agendas.index') }}"
                            class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400">Lihat
                            Semua</a>
                    </div>

                    <ul class="space-y-3">
                        @forelse ($upcomingAgendas as $agenda)
                            <li
                                class="flex items-center gap-4 rounded-xl border border-slate-100 p-3 transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60">
                                <div
                                    class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                    <span
                                        class="text-xs font-semibold uppercase">{{ \Carbon\Carbon::parse($agenda->date)->translatedFormat('M') }}</span>
                                    <span
                                        class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($agenda->date)->format('d') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $agenda->name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $agenda->agenda_code }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="flex flex-col items-center justify-center py-6 text-center">
                                <p class="text-sm italic text-slate-400 dark:text-slate-500">Tidak ada agenda dalam
                                    waktu dekat.
                                </p>
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Widget 2: Radar Pembinaan Aktif --}}
                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="mb-4 flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <h3 class="flex items-center text-base font-bold text-slate-800 dark:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="mr-2 h-5 w-5 text-red-500">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Radar Pembinaan (Aktif)
                        </h3>
                        <a href="{{ route('guidances.index') }}"
                            class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400">Kelola</a>
                    </div>

                    <ul class="space-y-3">
                        @forelse ($activeGuidances as $guidance)
                            <li
                                class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50/50 p-3 dark:border-red-900/30 dark:bg-red-500/5">
                                <div
                                    class="mt-0.5 shrink-0 rounded-full bg-red-100 p-1.5 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="truncate font-semibold text-slate-800 dark:text-slate-100">
                                            {{ $guidance->member->name }}</p>
                                        <span
                                            class="rounded bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700 dark:bg-red-900/40 dark:text-red-400">{{ $guidance->type }}</span>
                                    </div>
                                    <p class="mt-1 truncate text-xs text-slate-600 dark:text-slate-400"
                                        title="{{ $guidance->reason }}">Kasus: {{ $guidance->reason }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="flex flex-col items-center justify-center py-6 text-center">
                                <p class="text-sm italic text-slate-400 dark:text-slate-500">Aman! Tidak ada pengurus
                                    dalam
                                    pantauan.</p>
                            </li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ======================= STATISTIK WEBSITE ==================== --}}
        {{-- ============================================================ --}}
        <div x-show="statView === 'website'" x-cloak>

            {{-- KPI cards --}}
            <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Kunjungan Hari Ini</p>
                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ $websiteStats['kpi']['visits_today'] }}</p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-5.5 w-5.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Kunjungan 30 Hari
                                Terakhir</p>
                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ $websiteStats['kpi']['visits_30d'] }}</p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-5.5 w-5.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pengunjung Unik (30 Hari)
                            </p>
                            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">
                                {{ $websiteStats['kpi']['unique_visitors_30d'] }}</p>
                        </div>
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                                stroke="currentColor" class="h-5.5 w-5.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik traffic & halaman terpopuler --}}
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                <div
                    class="theme-transition col-span-1 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm xl:col-span-2 dark:border-slate-800 dark:bg-slate-900">
                    <h3
                        class="mb-4 border-b border-slate-100 pb-3 text-base font-bold text-slate-800 dark:border-slate-800 dark:text-white">
                        Tren Kunjungan (14 Hari Terakhir)
                    </h3>
                    <div class="relative h-64 w-full">
                        @if (array_sum($websiteStats['trafficSeries']) > 0)
                            <canvas id="trafficChart"></canvas>
                        @else
                            <div class="flex h-full items-center justify-center text-center">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="mx-auto mb-2 h-10 w-10 text-slate-300 dark:text-slate-700">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                    </svg>
                                    <p class="text-sm italic text-slate-400 dark:text-slate-500">Belum ada data
                                        kunjungan untuk ditampilkan.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div
                    class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3
                        class="mb-4 border-b border-slate-100 pb-3 text-base font-bold text-slate-800 dark:border-slate-800 dark:text-white">
                        Halaman Terpopuler
                    </h3>
                    <ul class="space-y-2.5">
                        @forelse ($websiteStats['topPages'] as $page)
                            <li
                                class="theme-transition flex items-center justify-between gap-3 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2.5 text-sm dark:border-slate-800 dark:bg-slate-800/60">
                                <span class="min-w-0 truncate font-medium text-slate-700 dark:text-slate-200"
                                    title="{{ $page->path }}">{{ $page->path }}</span>
                                <span
                                    class="shrink-0 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">{{ $page->total }}</span>
                            </li>
                        @empty
                            <li class="py-6 text-center text-sm italic text-slate-400 dark:text-slate-500">Belum ada
                                data kunjungan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = () => document.documentElement.classList.contains('dark');
            const charts = [];

            // ============ Komposisi Absensi ============
            const attendanceCtx = document.getElementById('attendanceChart');
            if (attendanceCtx) {
                const chart = new Chart(attendanceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                        datasets: [{
                            data: [
                                {{ $attendanceStats['H'] ?? 0 }},
                                {{ $attendanceStats['I'] ?? 0 }},
                                {{ $attendanceStats['S'] ?? 0 }},
                                {{ $attendanceStats['A'] ?? 0 }}
                            ],
                            backgroundColor: ['#3b82f6', '#eab308', '#a855f7', '#ef4444'],
                            borderColor: isDark() ? '#0f172a' : '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 11
                                    },
                                    color: isDark() ? '#cbd5e1' : '#475569'
                                }
                            }
                        }
                    }
                });
                charts.push({
                    chart,
                    type: 'doughnut'
                });
            }

            // ============ Tren Kunjungan Website ============
            const trafficCtx = document.getElementById('trafficChart');
            if (trafficCtx) {
                const chart = new Chart(trafficCtx, {
                    type: 'line',
                    data: {
                        labels: @json($websiteStats['trafficLabels']),
                        datasets: [{
                            label: 'Kunjungan',
                            data: @json($websiteStats['trafficSeries']),
                            fill: true,
                            tension: 0.35,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.12)',
                            pointRadius: 3,
                            pointBackgroundColor: '#2563eb'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: isDark() ? '#94a3b8' : '#64748b'
                                },
                                grid: {
                                    color: isDark() ? 'rgba(148,163,184,0.1)' : 'rgba(148,163,184,0.2)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: isDark() ? '#94a3b8' : '#64748b'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
                charts.push({
                    chart,
                    type: 'line'
                });
            }

            // Sinkronkan warna semua chart saat mode gelap/terang diganti
            document.addEventListener('katiber-theme-changed', function() {
                const dark = isDark();
                charts.forEach(({
                    chart,
                    type
                }) => {
                    if (type === 'doughnut') {
                        chart.data.datasets[0].borderColor = dark ? '#0f172a' : '#ffffff';
                        chart.options.plugins.legend.labels.color = dark ? '#cbd5e1' : '#475569';
                    } else if (type === 'line') {
                        chart.options.scales.y.ticks.color = dark ? '#94a3b8' : '#64748b';
                        chart.options.scales.y.grid.color = dark ? 'rgba(148,163,184,0.1)' : 'rgba(148,163,184,0.2)';
                        chart.options.scales.x.ticks.color = dark ? '#94a3b8' : '#64748b';
                    }
                    chart.update();
                });
            });
        });
    </script>
@endsection
