@extends('layouts.app')

@section('title', 'Rekap Absensi - KATIBER')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Rekapitulasi Kehadiran</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Matriks kehadiran pengurus per agenda untuk kebutuhan LPJ.
        </p>
    </div>

    <!-- Form Filter Bulan & Urutan -->
    <form action="{{ route('attendance-reports.index') }}" method="GET"
        class="theme-transition mb-6 flex flex-wrap items-end gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">

        <div>
            <label
                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pilih
                Bulan</label>
            <input type="month" name="month" value="{{ $monthFilter }}"
                class="rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:[color-scheme:dark]">
        </div>

        <div>
            <label
                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Urutkan
                Berdasarkan</label>
            <select name="sort"
                class="rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white min-w-[200px]">
                <option value="abjad" {{ $sortFilter == 'abjad' ? 'selected' : '' }}>Abjad Nama (A-Z)</option>
                <option value="divisi_jabatan" {{ $sortFilter == 'divisi_jabatan' ? 'selected' : '' }}>Divisi & Hierarki
                    Jabatan</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                class="rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                Tampilkan
            </button>
            <button type="submit" name="export" value="excel"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </button>
            <button type="submit" name="export" value="pdf"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </button>
        </div>

        <div class="ml-auto flex items-center gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Hadir</span>
            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Izin</span>
            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-purple-500"></span>Sakit</span>
            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-red-500"></span>Alpha</span>
        </div>
    </form>

    <!-- Matriks Absensi -->
    <div
        class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-3.5 dark:border-slate-800 dark:bg-slate-800/40">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Periode: {{ $monthName }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm whitespace-nowrap">
                <thead>
                    <tr
                        class="border-b border-slate-100 bg-slate-50/70 text-slate-500 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-400">
                        <th
                            class="border-r border-slate-100 px-4 py-3 text-xs font-semibold uppercase tracking-wide dark:border-slate-800">
                            No</th>
                        <th
                            class="border-r border-slate-100 px-4 py-3 text-xs font-semibold uppercase tracking-wide dark:border-slate-800">
                            Nama Pengurus</th>
                        @forelse($agendas as $agenda)
                            <th class="border-r border-slate-100 px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide dark:border-slate-800"
                                title="{{ $agenda->name }}">
                                {{ \Carbon\Carbon::parse($agenda->date)->format('d/m') }}<br>
                                <span
                                    class="font-normal normal-case text-slate-400 dark:text-slate-500">{{ $agenda->agenda_code }}</span>
                            </th>
                        @empty
                            <th class="px-4 py-3 text-center italic text-slate-400 dark:text-slate-500">Tidak ada agenda
                            </th>
                        @endforelse
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($members as $index => $member)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td
                                class="border-r border-slate-100 px-4 py-3 text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                {{ $index + 1 }}</td>
                            <td
                                class="border-r border-slate-100 px-4 py-3 font-medium text-slate-800 dark:border-slate-800 dark:text-white">
                                {{ $member->name }}</td>

                            @if ($agendas->count() > 0)
                                @foreach ($agendas as $agenda)
                                    @php
                                        $absen = null;
                                        if (isset($attendances[$member->id])) {
                                            $absen = $attendances[$member->id]->firstWhere('agenda_id', $agenda->id);
                                        }
                                        $status = $absen ? $absen->status : '-';

                                        $color = 'text-slate-300 dark:text-slate-600';
                                        if ($status == 'H') {
                                            $color = 'text-emerald-600 dark:text-emerald-400 font-bold';
                                        }
                                        if ($status == 'I') {
                                            $color = 'text-amber-600 dark:text-amber-400 font-bold';
                                        }
                                        if ($status == 'S') {
                                            $color = 'text-purple-600 dark:text-purple-400 font-bold';
                                        }
                                        if ($status == 'A') {
                                            $color = 'text-red-600 dark:text-red-400 font-bold';
                                        }
                                    @endphp
                                    <td
                                        class="border-r border-slate-100 px-3 py-3 text-center dark:border-slate-800 {{ $color }}">
                                        {{ $status }}
                                    </td>
                                @endforeach
                            @else
                                <td class="px-3 py-3 text-center text-slate-400 dark:text-slate-500">-</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
