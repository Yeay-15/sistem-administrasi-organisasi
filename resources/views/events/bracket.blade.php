@extends('layouts.app')

@section('title', 'Kelola Bagan - ' . $event->title)

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('events.index') }}"
            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Kelola Bagan &amp; Klasemen</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $event->title }} &bull; {{ $allTeams->count() }} tim terdaftar</p>
        </div>
    </div>

    {{-- ===================== GRUP & TIM ===================== --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-3 text-sm font-bold text-slate-700 dark:text-slate-300">Grup</h2>
            <div class="mb-4 space-y-2">
                @forelse ($groups as $group)
                    <div class="flex items-center justify-between rounded-lg border border-slate-100 px-3 py-2 dark:border-slate-800">
                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $group->name }}</span>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ $group->teams_count }} tim</span>
                            <form action="{{ route('events.bracket.groups.destroy', [$event, $group]) }}" method="POST"
                                onsubmit="return confirm('Hapus grup {{ $group->name }}? Tim di dalamnya akan kembali ke status belum ada grup, dan pertandingan grup ini ikut terhapus.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded p-1 text-slate-300 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">Belum ada grup. Buat grup dulu jika event ini memakai fase grup (opsional — bisa dilewati kalau langsung babak gugur).</p>
                @endforelse
            </div>
            <form action="{{ route('events.bracket.groups.store', $event) }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="name" required placeholder="Contoh: Grup A" maxlength="100"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                <button type="submit" class="shrink-0 rounded-lg bg-navy-700 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-navy-800">Tambah</button>
            </form>
        </div>

        <div class="theme-transition lg:col-span-2 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-700 dark:text-slate-300">Tim Peserta</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $allTeams->count() }} tim</span>
            </div>

            <div class="mb-4 max-h-72 space-y-2 overflow-y-auto pr-1">
                @forelse ($allTeams as $team)
                    <div class="flex items-center gap-3 rounded-lg border border-slate-100 px-3 py-2 dark:border-slate-800">
                        @if ($team->logo_path)
                            <img src="{{ asset('storage/' . $team->logo_path) }}" class="h-6 w-6 rounded-full object-cover" alt="">
                        @endif
                        <span class="flex-1 truncate text-sm text-slate-700 dark:text-slate-200">{{ $team->name }}</span>
                        <form action="{{ route('events.bracket.teams.assign-group', [$event, $team]) }}" method="POST" class="shrink-0">
                            @csrf @method('PATCH')
                            <select name="event_group_id" onchange="this.form.submit()"
                                class="rounded border border-slate-200 bg-white px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="">Belum ada grup</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}" {{ $team->event_group_id === $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </form>
                        <form action="{{ route('events.bracket.teams.destroy', [$event, $team]) }}" method="POST"
                            onsubmit="return confirm('Hapus tim {{ $team->name }}?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="shrink-0 rounded p-1 text-slate-300 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-slate-400">Belum ada tim ditambahkan.</p>
                @endforelse
            </div>

            <form action="{{ route('events.bracket.teams.store', $event) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="text" name="name" required placeholder="Nama sekolah / tim"
                    class="min-w-[10rem] flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                <select name="event_group_id" class="rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Belum ada grup</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
                <input type="file" name="logo" accept="image/*"
                    class="block max-w-[10rem] text-xs text-slate-600 file:mr-2 file:rounded-lg file:border-0 file:bg-navy-50 file:px-2.5 file:py-1.5 file:text-xs file:font-semibold file:text-navy-700 hover:file:bg-navy-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-navy-400">
                <button type="submit" class="shrink-0 rounded-lg bg-navy-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-navy-800">Tambah Tim</button>
            </form>
        </div>
    </div>

    {{-- ===================== FASE GRUP: KLASEMEN + PERTANDINGAN ===================== --}}
    @if ($groupsWithStandings->isNotEmpty())
        <h2 class="mb-4 mt-8 text-base font-bold text-slate-800 dark:text-white">Fase Grup</h2>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @foreach ($groupsWithStandings as $data)
                @php [$group, $standings] = [$data['group'], $data['standings']]; @endphp
                <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="mb-3 text-sm font-bold text-navy-800 dark:text-navy-300">Klasemen {{ $group->name }}</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-left text-slate-400 dark:border-slate-800">
                                    <th class="py-1.5 pr-2 font-semibold">Tim</th>
                                    <th class="px-1 py-1.5 text-center font-semibold">M</th>
                                    <th class="px-1 py-1.5 text-center font-semibold">S</th>
                                    <th class="px-1 py-1.5 text-center font-semibold">K</th>
                                    <th class="px-1 py-1.5 text-center font-semibold">SG</th>
                                    <th class="px-1 py-1.5 text-center font-semibold">Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($standings as $i => $row)
                                    <tr class="border-b border-slate-50 dark:border-slate-800/60 {{ $i < 2 ? 'bg-amber-50/50 dark:bg-amber-500/5' : '' }}">
                                        <td class="py-1.5 pr-2 font-semibold text-slate-700 dark:text-slate-200">{{ $row['team']->name }}</td>
                                        <td class="px-1 py-1.5 text-center text-slate-500 dark:text-slate-400">{{ $row['won'] }}</td>
                                        <td class="px-1 py-1.5 text-center text-slate-500 dark:text-slate-400">{{ $row['draw'] }}</td>
                                        <td class="px-1 py-1.5 text-center text-slate-500 dark:text-slate-400">{{ $row['lost'] }}</td>
                                        <td class="px-1 py-1.5 text-center text-slate-500 dark:text-slate-400">{{ $row['gd'] > 0 ? '+' : '' }}{{ $row['gd'] }}</td>
                                        <td class="px-1 py-1.5 text-center font-bold text-navy-800 dark:text-navy-300">{{ $row['points'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-4 text-center text-slate-400">Belum ada tim di grup ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mb-2 mt-4 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pertandingan</h4>
                    <div class="space-y-2">
                        @forelse ($group->matches as $match)
                            @include('events._match-card', ['match' => $match, 'editable' => true])
                        @empty
                            <p class="text-xs text-slate-400">Belum ada pertandingan di grup ini.</p>
                        @endforelse
                    </div>

                    @can('manage_events')
                    <form action="{{ route('events.bracket.matches.store', $event) }}" method="POST" class="mt-3 space-y-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                        @csrf
                        <input type="hidden" name="stage" value="group">
                        <input type="hidden" name="event_group_id" value="{{ $group->id }}">
                        <div class="flex gap-2">
                            <select name="team1_id" required class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <option value="">Tim 1</option>
                                @foreach ($group->teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                            <select name="team2_id" required class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <option value="">Tim 2</option>
                                @foreach ($group->teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-navy-100 px-3 py-1.5 text-xs font-semibold text-navy-800 transition hover:bg-navy-200 dark:bg-navy-500/10 dark:text-navy-300 dark:hover:bg-navy-500/20">+ Tambah Pertandingan Grup</button>
                    </form>
                    @endcan
                </div>
            @endforeach
        </div>
    @endif

    {{-- ===================== BABAK GUGUR ===================== --}}
    <h2 class="mb-4 mt-8 text-base font-bold text-slate-800 dark:text-white">Babak Gugur</h2>
    <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @can('manage_events')
        <form action="{{ route('events.bracket.matches.store', $event) }}" method="POST" class="mb-5 flex flex-wrap items-end gap-2 border-b border-slate-100 pb-5 dark:border-slate-800">
            @csrf
            <div>
                <label class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Babak</label>
                <select name="stage" required class="rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    @foreach (\App\Models\EventMatch::STAGES as $key => $label)
                        @if ($key !== 'group')
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="min-w-[9rem] flex-1">
                <label class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Tim 1</label>
                <select name="team1_id" required class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Pilih --</option>
                    @foreach ($allTeams as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[9rem] flex-1">
                <label class="mb-1 block text-[11px] font-semibold text-slate-500 dark:text-slate-400">Tim 2</label>
                <select name="team2_id" required class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Pilih --</option>
                    @foreach ($allTeams as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-navy-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-navy-800">Tambah Pertandingan</button>
        </form>
        <p class="-mt-3 mb-4 text-xs text-slate-400">Pilih babak sesuai keputusan federasi (bisa langsung ke Perempat Final tanpa 16 Besar, dsb.) dan pasangkan tim sesuai hasil grup/undian.</p>
        @endcan

        @if ($knockoutStages->isEmpty())
            <p class="text-center text-sm text-slate-400">Belum ada pertandingan babak gugur.</p>
        @else
            <div class="space-y-6">
                @foreach ($knockoutStages as $stageData)
                    <div>
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300">{{ $stageData['label'] }}</h3>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach ($stageData['matches'] as $match)
                                @include('events._match-card', ['match' => $match, 'editable' => true])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
