@extends('layouts.app')

@section('title', 'Kepanitiaan - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kepanitiaan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Histori kepanitiaan tiap acara beserta susunan panitianya.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @can('view_committees')
                <a href="{{ route('bidang-panitia.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Kelola Bidang Panitia
                </a>
            @endcan
            @can('manage_committees')
                <a href="{{ route('kepanitiaan.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Kepanitiaan
                </a>
            @endcan
        </div>
    </div>

    <div class="mb-4 theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form method="GET" action="{{ route('kepanitiaan.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Cari Nama Kepanitiaan</label>
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama kepanitiaan..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                <div class="relative">
                    <select name="status" class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">Semua Status</option>
                        @foreach (['Persiapan', 'Berjalan', 'Selesai'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Terapkan</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($committees as $committee)
            <a href="{{ route('kepanitiaan.show', $committee->id) }}"
                class="theme-transition group flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $committee->status === 'Selesai' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : ($committee->status === 'Berjalan' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400') }}">
                            {{ $committee->status }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $committee->committee_members_count }} anggota</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">{{ $committee->name }}</h3>
                    @if ($committee->type)
                        <p class="mt-0.5 text-xs text-slate-400">{{ $committee->type }}</p>
                    @endif
                </div>
                <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    @if ($committee->start_date)
                        {{ $committee->start_date->translatedFormat('d M Y') }}{{ $committee->end_date ? ' - ' . $committee->end_date->translatedFormat('d M Y') : '' }}
                    @else
                        Tanggal belum ditentukan
                    @endif
                </p>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 py-16 text-center text-sm text-slate-400 dark:border-slate-800 dark:text-slate-500">
                Belum ada data kepanitiaan.
            </div>
        @endforelse
    </div>
@endsection
