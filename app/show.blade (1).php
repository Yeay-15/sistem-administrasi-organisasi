@extends('layouts.app')

@section('title', 'Anggota Non-Pengurus - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Anggota Non-Pengurus</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Orang yang ikut kegiatan/kepanitiaan tapi bukan
                bagian struktur Pengurus — dosen pembimbing, alumni, relawan lepas, dsb.</p>
        </div>
        @can('manage_non_pengurus')
            <a href="{{ route('non-pengurus.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Anggota
            </a>
        @endcan
    </div>

    <div class="mb-4 theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form method="GET" action="{{ route('non-pengurus.index') }}" class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..."
                class="block w-full max-w-xs rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                Cari
            </button>
        </form>
    </div>

    <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Foto</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Asal Kampus / NIM</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($members as $member)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-5 py-4">
                                @if ($member->photo_url)
                                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}"
                                        class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">{{ $member->name }}</td>
                            <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">
                                {{ $member->university ?? '-' }}{{ $member->student_id ? ' · ' . $member->student_id : '' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('members.statistik', $member->id) }}" title="Lihat Statistik"
                                        class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M8.25 16.5v-4.5m4.5 4.5V9m4.5 7.5v-9" />
                                        </svg>
                                    </a>
                                    @can('manage_non_pengurus')
                                        <a href="{{ route('non-pengurus.edit', $member->id) }}" title="Edit"
                                            class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('manage_members')
                                        <a href="{{ route('members.promote-form', $member->id) }}" title="Jadikan Pengurus"
                                            class="rounded-lg p-2 text-emerald-600 transition hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete_non_pengurus')
                                        <form action="{{ route('non-pengurus.destroy', $member->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus data anggota ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center text-sm text-slate-400 dark:text-slate-500">
                                Belum ada data anggota non-pengurus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
