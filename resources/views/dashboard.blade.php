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

    {{-- Hero / sapaan --}}
    <div
        class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-600 to-indigo-700 px-6 py-7 text-white shadow-sm sm:px-8">
        <div class="pointer-events-none absolute -right-10 -top-16 h-56 w-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="relative flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-blue-100">{{ $greeting }},</p>
                <h1 class="mt-1 text-2xl font-bold sm:text-3xl">{{ $firstName }} 👋</h1>
                <p class="mt-2 max-w-md text-sm text-blue-100">
                    Berikut ringkasan aktivitas Sistem KATIBER untuk bulan
                    {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}.
                </p>
            </div>
            <div
                class="inline-flex items-center gap-2 self-start rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur sm:self-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                    stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <div
            class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Pengurus Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">{{ $kpi['total_members'] }}</p>
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
                    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">{{ $kpi['total_agendas'] }}</p>
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
                    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">{{ $kpi['total_incoming'] }}</p>
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
                    <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-white">{{ $kpi['total_outgoing'] }}</p>
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mx-auto mb-2 h-10 w-10 text-slate-300 dark:text-slate-700">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                        <p class="text-sm italic text-slate-400 dark:text-slate-500">Belum ada data absensi untuk
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="mr-2 h-4.5 w-4.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        Surat Masuk Terbaru
                    </h4>
                    <ul class="space-y-2.5">
                        @forelse ($recentIncoming as $in)
                            <li
                                class="theme-transition rounded-lg border border-slate-100 bg-slate-50 p-3 text-sm dark:border-slate-800 dark:bg-slate-800/60">
                                <span
                                    class="block text-xs text-slate-400 dark:text-slate-500">{{ \Carbon\Carbon::parse($in->received_date)->format('d M Y') }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $in->sender }}</span>
                                <span class="block truncate text-slate-500 dark:text-slate-400"
                                    title="{{ $in->subject }}">{{ $in->subject }}</span>
                            </li>
                        @empty
                            <li class="text-sm italic text-slate-400 dark:text-slate-500">Belum ada surat masuk.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Surat keluar --}}
                <div>
                    <h4 class="mb-3 flex items-center text-sm font-semibold text-purple-600 dark:text-purple-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="mr-2 h-4.5 w-4.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
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
                            <li class="text-sm italic text-slate-400 dark:text-slate-500">Belum ada surat keluar.</li>
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
                    class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400">Lihat Semua</a>
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
                            <p class="truncate font-semibold text-slate-800 dark:text-slate-100">{{ $agenda->name }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $agenda->agenda_code }}</p>
                        </div>
                    </li>
                @empty
                    <li class="flex flex-col items-center justify-center py-6 text-center">
                        <p class="text-sm italic text-slate-400 dark:text-slate-500">Tidak ada agenda dalam waktu dekat.
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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="h-4 w-4">
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
                        <p class="text-sm italic text-slate-400 dark:text-slate-500">Aman! Tidak ada pengurus dalam
                            pantauan.</p>
                    </li>
                @endforelse
            </ul>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('attendanceChart');
            if (!ctx) return;

            const isDark = () => document.documentElement.classList.contains('dark');

            const chart = new Chart(ctx, {
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

            // Sinkronkan warna chart saat mode gelap/terang diganti
            document.addEventListener('katiber-theme-changed', function() {
                const dark = isDark();
                chart.data.datasets[0].borderColor = dark ? '#0f172a' : '#ffffff';
                chart.options.plugins.legend.labels.color = dark ? '#cbd5e1' : '#475569';
                chart.update();
            });
        });
    </script>
@endsection
