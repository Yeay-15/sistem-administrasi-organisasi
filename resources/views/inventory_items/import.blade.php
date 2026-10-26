@extends('layouts.app')

@section('title', 'Impor Inventaris - KATIBER')

@section('content')
    <style>
        /* Tombol "Pilih file" bawaan browser, diberi gaya sendiri agar tidak perlu build ulang CSS. */
        .kt-file-input::file-selector-button {
            margin-right: 1rem;
            padding: .55rem 1rem;
            border: 0;
            border-radius: .5rem;
            background: rgb(239 246 255);
            color: rgb(37 99 235);
            font-weight: 600;
            cursor: pointer;
            transition: background-color .2s ease;
        }
        .kt-file-input::file-selector-button:hover { background: rgb(219 234 254); }
        .dark .kt-file-input::file-selector-button { background: rgba(59, 130, 246, .15); color: rgb(147 197 253); }
    </style>

    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('inventory-items.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Impor Inventaris dari Excel</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tambahkan banyak barang sekaligus lewat file Excel.</p>
            </div>
        </div>

        {{-- Hasil pemeriksaan: kesalahan per baris --}}
        @if (session('import_errors'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-500/30 dark:bg-red-500/10">
                <p class="text-sm font-semibold text-red-700 dark:text-red-300">
                    Tidak ada data yang diimpor. Ditemukan {{ session('import_error_total') }} baris bermasalah, perbaiki lalu unggah ulang:
                </p>
                <ul class="mt-3 space-y-2 text-sm text-red-700 dark:text-red-300">
                    @foreach (session('import_errors') as $error)
                        <li>
                            <span class="font-semibold">Baris {{ $error['line'] }}:</span>
                            {{ implode(' ', $error['messages']) }}
                        </li>
                    @endforeach
                </ul>
                @if (session('import_error_total') > count(session('import_errors')))
                    <p class="mt-3 text-xs text-red-600 dark:text-red-400">
                        ...dan {{ session('import_error_total') - count(session('import_errors')) }} baris lainnya.
                    </p>
                @endif
            </div>
        @endif

        {{-- Langkah 1: template --}}
        <div class="theme-transition mb-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white">1. Unduh template</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Isi data di sheet <span class="font-semibold">Data Inventaris</span> mulai baris ke-2. Aturan pengisian ada di sheet <span class="font-semibold">Petunjuk</span>.
                    </p>
                </div>
                <a href="{{ route('inventory-items.template') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Unduh Template Excel
                </a>
            </div>

            <div class="mt-5 overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            <th class="px-4 py-2.5">Kolom</th>
                            <th class="px-4 py-2.5">Wajib</th>
                            <th class="px-4 py-2.5">Isi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600 dark:divide-slate-800 dark:text-slate-300">
                        <tr><td class="px-4 py-2.5 font-medium">Nama Barang</td><td class="px-4 py-2.5">Ya</td><td class="px-4 py-2.5">Teks</td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Jumlah</td><td class="px-4 py-2.5">Ya</td><td class="px-4 py-2.5">Angka, atau bersama satuan (mis. "1 gulung")</td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Satuan, Tempat, Keterangan</td><td class="px-4 py-2.5">Tidak</td><td class="px-4 py-2.5">Teks bebas</td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Tanggal Ditambahkan</td><td class="px-4 py-2.5">Tidak</td><td class="px-4 py-2.5">dd/mm/yyyy, kosong = hari ini</td></tr>
                        <tr><td class="px-4 py-2.5 font-medium">Tanggal Kedaluwarsa</td><td class="px-4 py-2.5">Tidak</td><td class="px-4 py-2.5">dd/mm/yyyy atau bulan/tahun (mis. 12/27)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Langkah 2: unggah --}}
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <h2 class="text-base font-semibold text-slate-800 dark:text-white">2. Unggah file yang sudah diisi</h2>

            <form action="{{ route('inventory-items.import.store') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf

                <input type="file" name="file" accept=".xlsx,.xls" required
                    class="kt-file-input block w-full rounded-lg border border-slate-300 bg-white p-2 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Format .xlsx atau .xls, maksimal 5MB dan 1000 baris.</p>
                @error('file')
                    <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror

                <fieldset class="mt-6">
                    <legend class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">
                        Jika barang sudah ada (nama dan tempat sama)
                    </legend>
                    @php $onDuplicate = old('on_duplicate', 'skip'); @endphp
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <input type="radio" name="on_duplicate" value="skip" class="mt-1" @checked($onDuplicate === 'skip')>
                        <span class="text-sm text-slate-600 dark:text-slate-300">
                            <span class="font-semibold text-slate-800 dark:text-white">Lewati</span> &dash; data lama tidak diubah (aman).
                        </span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <input type="radio" name="on_duplicate" value="update" class="mt-1" @checked($onDuplicate === 'update')>
                        <span class="text-sm text-slate-600 dark:text-slate-300">
                            <span class="font-semibold text-slate-800 dark:text-white">Perbarui</span> &dash; data lama diganti sesuai isi file (kolom yang dikosongkan ikut dikosongkan, kecuali Tanggal Ditambahkan).
                        </span>
                    </label>
                    @error('on_duplicate')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </fieldset>

                <p class="mt-5 text-xs text-slate-500 dark:text-slate-400">
                    Semua baris diperiksa dulu. Jika ada satu saja yang salah, tidak ada data yang diimpor dan kamu akan melihat daftar barisnya.
                </p>

                <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <a href="{{ route('inventory-items.index') }}"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                        </svg>
                        Impor Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
