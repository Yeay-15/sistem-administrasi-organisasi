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
                        <td class="px-5 py-4 flex justify-center gap-2">
                            @can('manage_guests')
                            <a href="{{ route('guests.edit', $guest->id) }}"
                                class="rounded bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20">Edit</a>
                            <form action="{{ route('guests.destroy', $guest->id) }}" method="POST"
                                onsubmit="return confirm('Hapus data tamu ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-200 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">Hapus</button>
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
