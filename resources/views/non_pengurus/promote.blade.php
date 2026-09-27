@extends('layouts.app')

@section('title', 'Jadikan Pengurus - KATIBER')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('non-pengurus.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Jadikan "{{ $member->name }}" Pengurus</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Lengkapi divisi & jabatan strukturalnya. Histori
                    kepanitiaan dan kehadiran yang sudah ada tidak akan berubah.</p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <form action="{{ route('members.toggle-membership', $member->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Divisi</label>
                        <x-dropdown-select name="division_id" placeholder="Pilih Divisi" :required="true"
                            :selected="old('division_id')" :options="$divisions->pluck('name', 'id')" />
                        @error('division_id')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jabatan</label>
                        <x-dropdown-select name="position" placeholder="Pilih Jabatan" :required="true"
                            :selected="old('position')" :options="[
                                'Ketua Umum' => 'Ketua Umum',
                                'Sekretaris Umum' => 'Sekretaris Umum',
                                'Bendahara Umum' => 'Bendahara Umum',
                                'Ketua Divisi' => 'Ketua Divisi',
                                'Sekretaris Divisi' => 'Sekretaris Divisi',
                                'Anggota Divisi' => 'Anggota Divisi',
                            ]" />
                        @error('position')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Angkatan</label>
                        <input type="text" name="batch" value="{{ old('batch') }}" required placeholder="mis. 2024"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('batch')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Bergabung</label>
                        <input type="date" name="join_date" value="{{ old('join_date', now()->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:[color-scheme:dark]">
                        @error('join_date')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('non-pengurus.index') }}"
                        class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit"
                        class="rounded-lg bg-gradient-to-br from-emerald-600 to-emerald-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md">Jadikan Pengurus</button>
                </div>
            </form>
        </div>
    </div>
@endsection
