@extends('layouts.app')

@section('title', 'Tambah Ketua Umum - KATIBER')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('leaders.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Tambah Ketua Umum</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Data ini akan tampil di timeline "Estafet Kepemimpinan" pada halaman Tentang Kami.</p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8"
            x-data="{ preview: null }">
            <form action="{{ route('leaders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Foto</label>
                    <div class="flex items-center gap-4">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <template x-if="preview">
                                <img :src="preview" alt="Preview" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300 dark:text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" accept="image/png, image/jpeg, image/webp"
                                @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                            <p class="mt-1 text-xs text-slate-400">Opsional. Jika kosong, akan ditampilkan ikon siluet atau logo KATIBER. Maks. 3MB.</p>
                            @error('photo')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Ketua Umum</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Yahya Ayyash Alfaruq Lubis"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jurusan/Kampus <span class="font-normal text-slate-400">(Opsional)</span></label>
                    <input type="text" name="major" value="{{ old('major') }}" placeholder="Contoh: Teknik Informatika - Universitas Malikussaleh"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    <p class="mt-1 text-xs text-slate-400">Boleh dikosongkan dulu kalau belum diketahui — bisa dilengkapi belakangan.</p>
                    @error('major')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5 grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Mulai</label>
                        <input type="text" name="period_start" value="{{ old('period_start') }}" required placeholder="2014"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('period_start')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tahun Akhir</label>
                        <input type="text" name="period_end" value="{{ old('period_end') }}" required placeholder="2016"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        <p class="mt-1 text-xs text-slate-400">Boleh diisi "Sekarang" jika masih menjabat.</p>
                        @error('period_end')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Urutan Tampil (Opsional)</label>
                    <input type="number" name="order" min="0" value="{{ old('order', 0) }}"
                        class="w-full max-w-[160px] rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <p class="mt-1 text-xs text-slate-400">Angka lebih kecil tampil lebih dulu di timeline.</p>
                    @error('order')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex flex-col-reverse items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800 sm:flex-row">
                    <a href="{{ route('leaders.index') }}"
                        class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 sm:w-auto">Batal</a>
                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20 sm:w-auto">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
