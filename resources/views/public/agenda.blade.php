@extends('layouts.public')

@section('title', 'Agenda Kegiatan - KATIBER')

@section('content')
    <div x-data="publicAgendaView()">
        <section class="border-b border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Agenda Kegiatan</h1>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Jadwal kegiatan KATIBER yang akan dan telah berlangsung.</p>
                    </div>

                    {{-- Toggle Kalender / Daftar --}}
                    <div class="flex w-fit rounded-lg bg-white p-1 shadow-sm dark:bg-slate-800">
                        <button @click="tab = 'calendar'" type="button"
                            class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-semibold transition"
                            :class="tab === 'calendar' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            Kalender
                        </button>
                        <button @click="tab = 'list'" type="button"
                            class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-semibold transition"
                            :class="tab === 'list' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            Daftar
                        </button>
                    </div>
                </div>

                <form method="GET" class="mt-6 max-w-md">
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"
                            class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari agenda kegiatan..."
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    </div>
                </form>
            </div>
        </section>

        {{-- ============ MODE KALENDER ============ --}}
        <section x-show="tab === 'calendar'" x-cloak class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-100 p-4 dark:border-slate-800">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white" x-text="monthNames[month] + ' ' + year"></h2>
                    <div class="flex gap-2">
                        <button @click="prevMonth()"
                            class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <button @click="nextMonth()"
                            class="rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-800/30">
                    <template x-for="day in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']">
                        <div class="py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400" x-text="day"></div>
                    </template>
                </div>

                <div class="grid grid-cols-7 auto-rows-fr gap-px bg-slate-100 dark:bg-slate-800">
                    <template x-for="blankday in blankdays">
                        <div class="min-h-[90px] bg-white p-2 opacity-40 dark:bg-slate-900 sm:min-h-[110px]"></div>
                    </template>

                    <template x-for="date in no_of_days">
                        <div class="relative min-h-[90px] bg-white p-2 transition hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800/50 sm:min-h-[110px]">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-sm font-semibold"
                                :class="isToday(date) ? 'bg-blue-600 text-white' : 'text-slate-700 dark:text-slate-300'"
                                x-text="date"></span>

                            <div class="mt-2 flex flex-col gap-1.5">
                                <template x-for="event in getEvents(date)">
                                    <div class="group relative">
                                        <div class="truncate rounded px-2 py-1 text-[10px] font-semibold text-white shadow-sm"
                                            :style="event.colorStyle">
                                            <span x-text="event.title"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        {{-- ============ MODE DAFTAR ============ --}}
        <section x-show="tab === 'list'" x-cloak class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center gap-3">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Akan Datang</h2>
                <span class="h-px flex-1 bg-slate-100 dark:bg-slate-800"></span>
            </div>

            @if ($upcomingAgendas->isEmpty())
                <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada agenda kegiatan yang akan datang.</p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($upcomingAgendas as $agenda)
                        <div class="theme-transition flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                <span class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($agenda->date)->format('d') }}</span>
                                <span class="text-[11px] uppercase leading-none">{{ \Carbon\Carbon::parse($agenda->date)->translatedFormat('M') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $agenda->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $agenda->type }}</p>
                                @if ($agenda->person_in_charge)
                                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">PJ: {{ $agenda->person_in_charge }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $upcomingAgendas->links() }}
                </div>
            @endif

            <div class="mb-8 mt-16 flex items-center gap-3">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Telah Terlaksana</h2>
                <span class="h-px flex-1 bg-slate-100 dark:bg-slate-800"></span>
            </div>

            @if ($pastAgendas->isEmpty())
                <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada riwayat kegiatan.</p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($pastAgendas as $agenda)
                        <div class="theme-transition flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 opacity-80 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                <span class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($agenda->date)->format('d') }}</span>
                                <span class="text-[11px] uppercase leading-none">{{ \Carbon\Carbon::parse($agenda->date)->translatedFormat('M') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $agenda->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $agenda->type }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $pastAgendas->links() }}
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('publicAgendaView', () => ({
                tab: 'calendar',
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
                    return new Date(this.year, this.month, date).toDateString() === today.toDateString();
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
