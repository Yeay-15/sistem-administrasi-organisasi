@extends('layouts.app')

@section('title', 'Kelola Absensi - KATIBER')

@section('content')
    <!-- Informasi Detail Agenda -->
    <div class="theme-transition mb-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $agenda->name }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kode: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $agenda->agenda_code }}</span>
                    &bull; Tanggal: <span class="font-medium text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($agenda->date)->format('d M Y') }}</span>
                </p>
            </div>
            <a href="{{ route('agendas.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>
        <div class="mt-5 grid grid-cols-2 gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm dark:border-slate-800 dark:bg-slate-800/40 md:grid-cols-4">
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Jenis</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $agenda->type }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Penanggung Jawab</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $agenda->person_in_charge }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Status</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $agenda->status }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Keterangan</span>
                <span class="text-slate-700 dark:text-slate-200">{{ $agenda->notes ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Form Input Absensi Masal -->
    <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/40">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Form Absensi Pengurus</h3>
            <span class="text-sm text-slate-500 dark:text-slate-400">Total: {{ $members->count() }} Pengurus Aktif</span>
        </div>

        <form action="{{ route('attendances.store', $agenda->id) }}" method="POST">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">No</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">NIM & Nama</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Divisi & Jabatan</th>
                            <th class="w-48 px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status Kehadiran</th>
                            <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Keterangan (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($members as $index => $member)
                            @php
                                $absen = $existingAttendances->get($member->id);
                                $currentStatus = $absen ? $absen->status : 'A';
                                $currentNotes = $absen ? $absen->notes : '';
                            @endphp
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-5 py-3.5 text-sm text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="block text-sm font-semibold text-slate-800 dark:text-white">{{ $member->name }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $member->student_id }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="block text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $member->division->name ?? '-' }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $member->position }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <select name="attendances[{{ $member->id }}][status]" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                        <option value="H" {{ $currentStatus == 'H' ? 'selected' : '' }}>Hadir (H)</option>
                                        <option value="I" {{ $currentStatus == 'I' ? 'selected' : '' }}>Izin (I)</option>
                                        <option value="S" {{ $currentStatus == 'S' ? 'selected' : '' }}>Sakit (S)</option>
                                        <option value="A" {{ $currentStatus == 'A' ? 'selected' : '' }}>Alpha (A)</option>
                                    </select>
                                </td>
                                <td class="px-5 py-3.5">
                                    <input type="text" name="attendances[{{ $member->id }}][notes]" value="{{ $currentNotes }}" placeholder="Catatan..."
                                        class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-300 dark:text-slate-700">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.106A12.318 12.318 0 008.624 15c-2.331 0-4.512.645-6.374 1.766L2.25 17c0 1.6.5 3.086 1.352 4.31M15 19.128V21m-6.75-3.235A4.125 4.125 0 0112 14.25a4.125 4.125 0 013.75 3.515M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        <p class="text-sm text-slate-400 dark:text-slate-500">Tidak ada pengurus berstatus aktif untuk diabsen.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-slate-100 bg-slate-50/70 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/40">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan / Update Absensi
                </button>
            </div>
        </form>
    </div>
@endsection
