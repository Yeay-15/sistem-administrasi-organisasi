@extends('layouts.app')

@section('title', 'Buku Tamu - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Buku Tamu Eksternal</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Catatan kehadiran tamu undangan, instansi, alumni, dan
                demisioner.</p>
        </div>
        @can('manage_guests')
        <a href="{{ route('guests.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
            + Tambah Tamu
        </a>
        @endcan
    </div>

    <div
        class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-x-auto">
        <table class="w-full border-collapse text-left text-sm whitespace-nowrap">
            <thead>
                <tr
                    class="border-b border-slate-100 bg-slate-50/70 text-slate-500 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-400">
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide">Acara / Agenda</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide">Nama Tamu</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide">Instansi / Organisasi</th>
                    <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide">Perwakilan / Jabatan</th>
                    <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($guests as $guest)
                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <td class="px-5 py-4">
                            <span class="block font-medium text-slate-800 dark:text-white">{{ $guest->agenda->name }}</span>
                            <span
                                class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($guest->agenda->date)->format('d M Y') }}</span>
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-800 dark:text-white">{{ $guest->name }}</td>
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $guest->institution }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $guest->role ?: '-' }}</td>
                        <td class="px-5 py-4 flex justify-center gap-1.5">
                            @can('manage_guests')
                            <a href="{{ route('guests.edit', $guest->id) }}" title="Edit"
                                class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-3.75-3.75" />
                                </svg>
                            </a>
                            @endcan
                            @can('delete_guests')
                            <form action="{{ route('guests.destroy', $guest->id) }}" method="POST"
                                onsubmit="return confirm('Hapus data tamu ini?');" class="inline">
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada
                            data tamu undangan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
