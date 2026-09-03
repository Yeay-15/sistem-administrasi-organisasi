@extends('layouts.app')

@section('title', 'Data Pengurus - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen Pengurus</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Data lengkap pengurus beserta jabatan dan status
                keaktifan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if ($members->isNotEmpty())
                {{-- Tombol Export diupdate menggunakan array_merge agar parameter filter (division_id & batch) ikut terbawa --}}
                <a href="{{ route('members.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('members.index', array_merge(request()->query(), ['export' => 'pdf'])) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            @endif
            @can('manage_members')
                <a href="{{ route('members.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pengurus
                </a>
            @endcan
        </div>
    </div>

    {{-- ==================== FORM FILTER ==================== --}}
    <div
        class="mb-4 theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form method="GET" action="{{ route('members.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">

            {{-- Filter Divisi --}}
            <div class="flex-1">
                <label for="division_id" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Filter
                    Divisi</label>
                <select name="division_id" id="division_id"
                    class="block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Angkatan --}}
            <div class="w-full sm:w-48">
                <label for="batch"
                    class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Angkatan</label>
                <select name="batch" id="batch"
                    class="block w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Semua Angkatan</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch }}" {{ request('batch') == $batch ? 'selected' : '' }}>
                            {{ $batch }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                    Terapkan
                </button>
                @if (request()->hasAny(['division_id', 'batch']))
                    <a href="{{ route('members.index') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
    {{-- ==================== END FORM FILTER ==================== --}}

    <div
        class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                        <th
                            class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            No</th>
                        <th
                            class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Foto</th>
                        <th
                            class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            NIM</th>
                        <th
                            class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Nama</th>
                        <th
                            class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Divisi & Jabatan</th>
                        <th
                            class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Angkatan</th>
                        <th
                            class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Status</th>
                        <th
                            class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($members as $index => $member)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-5 py-4">
                                @if ($member->photo_url)
                                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}"
                                        class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                                @else
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $member->student_id }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">
                                {{ $member->name }}
                                @if ($member->major || $member->university)
                                    <p class="mt-0.5 text-xs font-normal text-slate-400 dark:text-slate-500">
                                        {{ $member->major }}{{ $member->major && $member->university ? ' - ' : '' }}{{ $member->university }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="block text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $member->division->name ?? '-' }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $member->position }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $member->batch }}</td>
                            <td class="px-5 py-4 text-center">
                                @if ($member->status == 'Aktif')
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $member->status }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $member->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('manage_members')
                                        <a href="{{ route('members.edit', $member->id) }}" title="Edit"
                                            class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 8.25l-3.75-3.75" />
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete_members')
                                        <form action="{{ route('members.destroy', $member->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus data pengurus ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="h-10 w-10 text-slate-300 dark:text-slate-700">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.106A12.318 12.318 0 008.624 15c-2.331 0-4.512.645-6.374 1.766L2.25 17c0 1.6.5 3.086 1.352 4.31M15 19.128V21m-6.75-3.235A4.125 4.125 0 0112 14.25a4.125 4.125 0 013.75 3.515M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada data pengurus.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
