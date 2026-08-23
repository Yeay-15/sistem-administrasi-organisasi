@extends('layouts.app')

@section('title', 'Data Agenda - KATIBER')

@section('content')
    <div x-data="agendaView()" class="mx-auto w-full">

        {{-- Header & Tab Toggle --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen Agenda & Absensi</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Catat agenda rapat, pleno, dan kegiatan rutin
                    organisasi.</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Toggle View (Kalender vs Tabel) --}}
                <div class="flex rounded-lg bg-slate-100 p-1 dark:bg-slate-800">
                    <button @click="view = 'calendar'"
                        class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-semibold transition"
                        :class="view === 'calendar' ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-700 dark:text-blue-400' :
                            'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        Kalender
                    </button>
                    <button @click="view = 'table'"
                        class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-semibold transition"
                        :class="view === 'table' ? 'bg-white text-blue-600 shadow-sm dark:bg-slate-700 dark:text-blue-400' :
                            'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        Daftar
                    </button>
                </div>

                <a href="{{ route('agendas.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Agenda
                </a>
            </div>
        </div>

        {{-- ================= MODE KALENDER ================= --}}
        <!-- Hapus overflow-hidden agar tooltip tidak terpotong -->
        <div x-show="view === 'calendar'" x-cloak
            class="theme-transition rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div
                class="flex items-center justify-between border-b border-slate-100 p-4 rounded-t-2xl dark:border-slate-800">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" x-text="monthNames[month] + ' ' + year"></h2>
                <div class="flex gap-2">
                    <button @click="prevMonth()"
                        class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button @click="nextMonth()"
                        class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <div
                class="grid grid-cols-7 border-b border-slate-100 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/30">
                <template x-for="day in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']">
                    <div class="py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                        x-text="day"></div>
                </template>
            </div>

            <div class="grid grid-cols-7 auto-rows-fr bg-slate-100 gap-px rounded-b-2xl dark:bg-slate-800">
                <template x-for="blankday in blankdays">
                    <div class="bg-white min-h-[110px] p-2 dark:bg-slate-900 opacity-40"></div>
                </template>

                <template x-for="date in no_of_days">
                    <div
                        class="bg-white min-h-[110px] p-2 dark:bg-slate-900 transition hover:bg-slate-50 dark:hover:bg-slate-800/50 relative">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-sm font-semibold"
                            :class="isToday(date) ? 'bg-blue-600 text-white' : 'text-slate-700 dark:text-slate-300'"
                            x-text="date"></span>

                        <div class="mt-2 flex flex-col gap-1.5">
                            <template x-for="event in getEvents(date)">

                                {{-- KOTAK AGENDA DENGAN FLOATING TOOLTIP --}}
                                <div class="group relative">
                                    <!-- Label Agenda di Kalender -->
                                    <div class="truncate rounded px-2 py-1 text-[10px] font-semibold text-white shadow-sm cursor-pointer transition hover:opacity-80"
                                        :class="event.colorClass">
                                        <span x-text="event.title"></span>
                                    </div>

                                    <!-- Floating Tooltip (Muncul saat di-hover) -->
                                    <div
                                        class="pointer-events-none invisible absolute bottom-full left-1/2 z-[100] mb-2 w-52 -translate-x-1/2 translate-y-1 opacity-0 transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                                        <div
                                            class="rounded-xl border border-slate-200 bg-white p-3 shadow-xl dark:border-slate-700 dark:bg-slate-800 text-left">
                                            <p class="mb-2 text-xs font-bold text-slate-800 dark:text-white"
                                                x-text="event.title"></p>

                                            <div class="flex flex-col gap-1 text-[10px] text-slate-600 dark:text-slate-300">
                                                <div
                                                    class="flex justify-between gap-2 border-b border-slate-100 pb-1 dark:border-slate-700">
                                                    <span class="font-semibold text-slate-400">Kode:</span>
                                                    <span class="truncate font-medium" x-text="event.code"></span>
                                                </div>
                                                <div
                                                    class="flex justify-between gap-2 border-b border-slate-100 pb-1 dark:border-slate-700">
                                                    <span class="font-semibold text-slate-400">Divisi:</span>
                                                    <span
                                                        class="truncate text-right font-medium text-blue-600 dark:text-blue-400"
                                                        x-text="event.pic" :title="event.pic"></span>
                                                </div>
                                                <div class="flex justify-between gap-2 pt-0.5">
                                                    <span class="font-semibold text-slate-400">Status:</span>
                                                    <span class="font-bold"
                                                        :class="event.status === 'Selesai' ? 'text-emerald-500' : (event
                                                            .status === 'Terjadwal' ? 'text-amber-500' :
                                                            'text-red-500')"
                                                        x-text="event.status"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Segitiga Panah Bawah -->
                                        <div
                                            class="absolute -bottom-1.5 left-1/2 h-3 w-3 -translate-x-1/2 rotate-45 border-b border-r border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                        </div>
                                    </div>
                                </div>

                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ================= MODE TABEL ================= --}}
        <div x-show="view === 'table'" x-cloak
            class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                            <th
                                class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Tanggal</th>
                            <th
                                class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Kode</th>
                            <th
                                class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Nama Agenda</th>
                            <th
                                class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                PIC / Divisi</th>
                            <th
                                class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Status</th>
                            <th
                                class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Aksi & Absensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($agendas as $agenda)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($agenda->date)->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $agenda->agenda_code }}
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">
                                    {{ $agenda->name }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    {{ $agenda->person_in_charge }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if ($agenda->status == 'Selesai')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $agenda->status }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $agenda->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('agendas.show', $agenda->id) }}" title="Kelola Absensi"
                                            class="rounded-lg p-2 text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('agendas.edit', $agenda->id) }}" title="Edit"
                                            class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 8.25l-3.75-3.75" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('agendas.destroy', $agenda->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus agenda ini beserta semua data absensinya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"
                                                    class="h-4.5 w-4.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="h-10 w-10 text-slate-300 dark:text-slate-700">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada data agenda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Script Logika Kalender Alpine.js --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('agendaView', () => ({
                view: 'calendar',
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ],
                blankdays: [],
                no_of_days: [],
                events: @json($calendarEvents),

                init() {
                    this.getNoOfDays();
                },

                isToday(date) {
                    const today = new Date();
                    return new Date(this.year, this.month, date).toDateString() === today
                .toDateString();
                },

                getNoOfDays() {
                    let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    let dayOfWeek = new Date(this.year, this.month).getDay();
                    let blankdaysArray = [];
                    let firstDay = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

                    for (let i = 1; i <= firstDay; i++) {
                        blankdaysArray.push(i);
                    }

                    let daysArray = [];
                    for (let i = 1; i <= daysInMonth; i++) {
                        daysArray.push(i);
                    }

                    this.blankdays = blankdaysArray;
                    this.no_of_days = daysArray;
                },

                prevMonth() {
                    if (this.month === 0) {
                        this.month = 11;
                        this.year--;
                    } else {
                        this.month--;
                    }
                    this.getNoOfDays();
                },

                nextMonth() {
                    if (this.month === 11) {
                        this.month = 0;
                        this.year++;
                    } else {
                        this.month++;
                    }
                    this.getNoOfDays();
                },

                getEvents(date) {
                    let d = date < 10 ? '0' + date : date;
                    let m = (this.month + 1) < 10 ? '0' + (this.month + 1) : (this.month + 1);
                    let formattedDate = `${this.year}-${m}-${d}`;

                    return this.events.filter(e => e.date === formattedDate);
                }
            }))
        });
    </script>
@endsection
