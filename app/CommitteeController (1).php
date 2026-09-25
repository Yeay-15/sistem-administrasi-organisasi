@extends('layouts.app')

@section('title', 'Statistik ' . $member->name . ' - KATIBER')

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ $member->membership_type === 'Pengurus' ? route('members.index') : route('non-pengurus.index') }}"
            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Statistik Anggota</h1>
    </div>

    {{-- Header Profil --}}
    <div class="theme-transition mb-6 flex flex-col gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:flex-row sm:items-center">
        @if ($member->photo_url)
            <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="h-20 w-20 shrink-0 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">
        @else
            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-slate-100 text-2xl font-semibold text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
        @endif
        <div class="flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ $member->name }}</h2>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                    {{ $member->membership_type === 'Pengurus' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                    {{ $member->membership_type }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                @if ($member->membership_type === 'Pengurus')
                    {{ $member->position }} · {{ $member->division->name ?? '-' }}
                @else
                    {{ $member->university ?? 'Tanpa keterangan kampus/instansi' }}
                @endif
                {{ $member->student_id ? ' · NIM ' . $member->student_id : '' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can('manage_members')
                @if ($member->membership_type === 'Pengurus')
                    <form action="{{ route('members.toggle-membership', $member->id) }}" method="POST" onsubmit="return confirm('Pindahkan {{ $member->name }} ke Anggota Non-Pengurus?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Jadikan Non-Pengurus</button>
                    </form>
                @else
                    <a href="{{ route('members.promote-form', $member->id) }}" class="rounded-lg border border-emerald-200 px-4 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-400 dark:hover:bg-emerald-500/10">Jadikan Pengurus</a>
                @endif
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- ==================== KEHADIRAN ==================== --}}
        <div class="space-y-6 lg:col-span-1">
            <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Kehadiran Agenda Organisasi</h3>

                <div class="mb-4 flex items-center gap-4">
                    <div class="relative h-24 w-24 shrink-0">
                        <svg viewBox="0 0 36 36" class="h-24 w-24 -rotate-90">
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.2"
                                class="stroke-slate-100 dark:stroke-slate-800"></circle>
                            <circle cx="18" cy="18" r="15.9155" fill="none" stroke-width="3.2" stroke-linecap="round"
                                stroke-dasharray="{{ $persentaseKehadiran }} {{ 100 - $persentaseKehadiran }}"
                                class="stroke-blue-600 dark:stroke-blue-500"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-base font-bold text-slate-800 dark:text-white">{{ $persentaseKehadiran }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalHadir }} <span class="text-sm font-normal text-slate-400">/ {{ $totalAgenda }} agenda</span></p>
                        <p class="text-xs text-slate-400">Total seluruh agenda organisasi (rapat & kegiatan)</p>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <div class="text-center">
                        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $attendanceByStatus['H'] ?? 0 }}</p>
                        <p class="text-[11px] text-slate-400">Hadir</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $attendanceByStatus['I'] ?? 0 }}</p>
                        <p class="text-[11px] text-slate-400">Izin</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $attendanceByStatus['S'] ?? 0 }}</p>
                        <p class="text-[11px] text-slate-400">Sakit</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ $attendanceByStatus['A'] ?? 0 }}</p>
                        <p class="text-[11px] text-slate-400">Alpha</p>
                    </div>
                </div>
            </div>

            @if ($totalAgendaByType->isNotEmpty())
                <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Kehadiran per Jenis Agenda</h3>
                    <div class="space-y-3">
                        @foreach ($totalAgendaByType as $type => $total)
                            @php $hadir = $attendanceByType[$type] ?? 0; $pct = $total > 0 ? round(($hadir / $total) * 100) : 0; @endphp
                            <div>
                                <div class="mb-1 flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-600 dark:text-slate-300">{{ $type }}</span>
                                    <span class="text-slate-400">{{ $hadir }}/{{ $total }} ({{ $pct }}%)</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                    <div class="h-full rounded-full bg-blue-600" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($committeeAttendance->isNotEmpty())
                <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Kehadiran Rapat per Kepanitiaan</h3>
                    <div class="space-y-3">
                        @foreach ($committeeAttendance as $row)
                            @php $pct = $row['total_agenda'] > 0 ? round(($row['hadir'] / $row['total_agenda']) * 100) : null; @endphp
                            <div>
                                <div class="mb-1 flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-600 dark:text-slate-300">{{ $row['committee']->name }}</span>
                                    <span class="text-slate-400">
                                        @if ($row['total_agenda'] > 0)
                                            {{ $row['hadir'] }}/{{ $row['total_agenda'] }} ({{ $pct }}%)
                                        @else
                                            Belum ada agenda tertaut
                                        @endif
                                    </span>
                                </div>
                                @if ($row['total_agenda'] > 0)
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                        <div class="h-full rounded-full bg-indigo-600" style="width: {{ $pct }}%"></div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ==================== HISTORI KEPANITIAAN ==================== --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Histori Kepanitiaan — Teras Panitia</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($terasPanitia as $cm)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div>
                                <a href="{{ route('kepanitiaan.show', $cm->committee_id) }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">{{ $cm->committee->name ?? '(kepanitiaan dihapus)' }}</a>
                                <p class="text-xs text-slate-400">{{ $cm->committee->start_date?->translatedFormat('d M Y') ?? 'Tanggal belum ditentukan' }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">{{ $cm->position_category }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Belum pernah menjabat sebagai Teras Panitia.</p>
                    @endforelse
                </div>
            </div>

            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Histori Kepanitiaan — Anggota Bidang</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($anggotaBidang as $cm)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div>
                                <a href="{{ route('kepanitiaan.show', $cm->committee_id) }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600 dark:text-white dark:hover:text-blue-400">{{ $cm->committee->name ?? '(kepanitiaan dihapus)' }}</a>
                                <p class="text-xs text-slate-400">{{ $cm->committee->start_date?->translatedFormat('d M Y') ?? 'Tanggal belum ditentukan' }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $cm->position_category }}{{ $cm->bidang ? ' · ' . $cm->bidang->name : '' }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-slate-400 dark:text-slate-500">Belum pernah menjadi Anggota/Ketua Bidang.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
