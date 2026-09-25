@extends('layouts.app')

@section('title', 'Bidang Panitia - KATIBER')

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('kepanitiaan.index') }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Bidang Panitia</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Daftar master nama bidang/seksi kepanitiaan (mis. Sie Acara, Sie Konsumsi), dipakai berulang lintas kepanitiaan.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:bg-red-500/10 dark:text-red-400">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Bidang</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Keterangan</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Dipakai</th>
                        @can('manage_committees')
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($bidangList as $bidang)
                        <tr x-data="{ editing: false }">
                            <td class="px-5 py-3.5 align-top">
                                <span x-show="!editing" class="text-sm font-semibold text-slate-800 dark:text-white">{{ $bidang->name }}</span>
                                <input x-show="editing" x-cloak type="text" name="name" form="edit-bidang-{{ $bidang->id }}" value="{{ $bidang->name }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </td>
                            <td class="px-5 py-3.5 align-top text-sm text-slate-500 dark:text-slate-400">
                                <span x-show="!editing">{{ $bidang->description ?? '-' }}</span>
                                <input x-show="editing" x-cloak type="text" name="description" form="edit-bidang-{{ $bidang->id }}" value="{{ $bidang->description }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </td>
                            <td class="px-5 py-3.5 text-center align-top text-sm text-slate-500 dark:text-slate-400">{{ $bidang->committee_members_count }}x</td>
                            @can('manage_committees')
                                <td class="px-5 py-3.5 text-center align-top">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button x-show="editing" x-cloak type="submit" form="edit-bidang-{{ $bidang->id }}" title="Simpan"
                                            class="rounded-lg p-2 text-emerald-600 transition hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="editing = !editing" :title="editing ? 'Batal' : 'Edit'"
                                            class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                            <svg x-show="!editing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-3.75-3.75" />
                                            </svg>
                                            <svg x-show="editing" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        @can('delete_committees')
                                            <form action="{{ route('bidang-panitia.destroy', $bidang->id) }}" method="POST" onsubmit="return confirm('Hapus bidang ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Hapus" class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada bidang panitia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Form edit tiap baris ditaruh di sini (di luar <table>, karena <form>
            bukan elemen anak yang valid untuk <table>/<tr>). Input & tombol di
            dalam tabel terhubung ke form ini lewat atribut form="edit-bidang-x". --}}
            @foreach ($bidangList as $bidang)
                <form id="edit-bidang-{{ $bidang->id }}" action="{{ route('bidang-panitia.update', $bidang->id) }}" method="POST" class="hidden">
                    @csrf @method('PUT')
                </form>
            @endforeach
        </div>

        @can('manage_committees')
            <div class="theme-transition h-fit rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Tambah Bidang Baru</h3>
                <form action="{{ route('bidang-panitia.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-slate-400">Nama Bidang</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="mis. Sie Acara"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('name')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-slate-400">Keterangan (opsional)</label>
                        <input type="text" name="description" value="{{ old('description') }}"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('description')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md">Tambahkan</button>
                </form>
            </div>
        @endcan
    </div>
@endsection
