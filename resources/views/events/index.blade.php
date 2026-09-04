@extends('layouts.app')

@section('title', 'Event Unggulan - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Event Unggulan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola acara besar seperti KATIBER Cup — tampilkan sebagai highlight di beranda &amp; pita pengumuman, lengkap dengan halaman info dan bagan pertandingan.</p>
        </div>
        @can('manage_events')
        <a href="{{ route('events.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Event Baru
        </a>
        @endcan
    </div>

    <form method="GET" class="mb-5">
        <div class="relative max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"
                class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama event..."
                class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
        </div>
    </form>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($events as $event)
            <div class="theme-transition flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="relative h-36 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900">
                    @if ($event->poster_path)
                        <img src="{{ asset('storage/' . $event->poster_path) }}" alt="{{ $event->title }}"
                            class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-300 dark:text-slate-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                            </svg>
                        </div>
                    @endif
                    <span class="absolute right-2.5 top-2.5 rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide shadow
                        {{ $event->status === 'active' ? 'bg-emerald-500 text-white' : ($event->status === 'archived' ? 'bg-slate-500 text-white' : 'bg-amber-400 text-slate-900') }}">
                        {{ $event->status === 'active' ? 'Aktif' : ($event->status === 'archived' ? 'Arsip' : 'Draft') }}
                    </span>
                </div>

                <div class="flex flex-1 flex-col p-4">
                    <h2 class="text-base font-bold text-slate-800 dark:text-white">{{ $event->title }}</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ $event->event_start_date->translatedFormat('d M Y') }}
                        @if ($event->event_end_date && ! $event->event_end_date->isSameDay($event->event_start_date))
                            &ndash; {{ $event->event_end_date->translatedFormat('d M Y') }}
                        @endif
                    </p>
                    @if ($event->has_bracket)
                        <p class="mt-1.5 inline-flex w-fit items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400">
                            🏆 Bagan {{ $event->team_count }} Tim &bull; {{ $event->teams_count }}/{{ $event->team_count }} terdaftar
                        </p>
                    @endif

                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 border-t border-slate-100 pt-3 text-xs dark:border-slate-800">
                        @can('manage_events')
                        <form action="{{ route('events.toggle-homepage', $event) }}" method="POST" class="flex items-center gap-1.5">
                            @csrf @method('PATCH')
                            <button type="submit" title="Klik untuk {{ $event->show_on_homepage ? 'sembunyikan dari' : 'tampilkan di' }} beranda"
                                class="flex items-center gap-1.5 {{ $event->show_on_homepage ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">
                                <span class="relative inline-flex h-4 w-7 items-center rounded-full transition {{ $event->show_on_homepage ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700' }}">
                                    <span class="inline-block h-3 w-3 transform rounded-full bg-white shadow transition {{ $event->show_on_homepage ? 'translate-x-3.5' : 'translate-x-0.5' }}"></span>
                                </span>
                                Beranda
                            </button>
                        </form>
                        <form action="{{ route('events.toggle-announcement', $event) }}" method="POST" class="flex items-center gap-1.5">
                            @csrf @method('PATCH')
                            <button type="submit" title="Klik untuk {{ $event->show_announcement_bar ? 'matikan' : 'aktifkan' }} pita pengumuman"
                                class="flex items-center gap-1.5 {{ $event->show_announcement_bar ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}">
                                <span class="relative inline-flex h-4 w-7 items-center rounded-full transition {{ $event->show_announcement_bar ? 'bg-amber-500' : 'bg-slate-300 dark:bg-slate-700' }}">
                                    <span class="inline-block h-3 w-3 transform rounded-full bg-white shadow transition {{ $event->show_announcement_bar ? 'translate-x-3.5' : 'translate-x-0.5' }}"></span>
                                </span>
                                Pengumuman
                            </button>
                        </form>
                        @endcan
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <a href="{{ route('public.events.show', $event) }}" target="_blank"
                            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300" title="Lihat halaman publik">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                        @if ($event->has_bracket)
                            <a href="{{ route('events.bracket', $event) }}" title="Kelola Bagan &amp; Tim"
                                class="rounded-lg p-2 text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15M4.5 9h15M4.5 15h15" />
                                </svg>
                            </a>
                        @endif
                        <a href="{{ route('events.updates.index', $event) }}" title="Kelola Info Terkini"
                            class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </a>
                        @can('manage_events')
                        <a href="{{ route('events.edit', $event) }}" title="Edit"
                            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                            </svg>
                        </a>
                        @endcan
                        @can('delete_events')
                        <form action="{{ route('events.destroy', $event) }}" method="POST"
                            onsubmit="return confirm('Hapus event \'{{ $event->title }}\'? Seluruh data tim, bagan, dan info terkini ikut terhapus.');" class="ml-auto">
                            @csrf @method('DELETE')
                            <button type="submit" title="Hapus"
                                class="rounded-lg p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada Event Unggulan. Buat satu untuk menyorot acara besar seperti KATIBER Cup di beranda.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $events->links() }}
    </div>
@endsection
