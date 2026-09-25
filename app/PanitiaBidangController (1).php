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
                                <input x-show="editing" type="text" name="name" form="edit-bidang-{{ $bidang->id }}" value="{{ $bidang->name }}"
                                    class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </td>
                            <td class="px-5 py-3.5 align-top text-sm text-slate-500 dark:text-slate-400">
                                <span x-show="!editing">{{ $bidang->description ?? '-' }}</span>
                                <input x-show="editing" type="text" name="description" form="edit-bidang-{{ $bidang->id }}" value="{{ $bidang->description }}"
                                    class="w-full rounded-lg border-slate-300 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </td>
                            <td class="px-5 py-3.5 text-center align-top text-sm text-slate-500 dark:text-slate-400">{{ $bidang->committee_members_count }}x</td>
                            @can('manage_committees')
                                <td class="px-5 py-3.5 text-center align-top">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button x-show="editing" type="submit" form="edit-bidang-{{ $bidang->id }}" class="rounded-lg p-2 text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-500/10">Simpan</button>
                                        <button type="button" @click="editing = !editing" class="rounded-lg p-2 text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                            <span x-show="!editing">Edit</span>
                                            <span x-show="editing">Batal</span>
                                        </button>
                                        @can('delete_committees')
                                            <form action="{{ route('bidang-panitia.destroy', $bidang->id) }}" method="POST" onsubmit="return confirm('Hapus bidang ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">Hapus</button>
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
