@extends('layouts.app')

@section('title', 'Unggah Foto Galeri - KATIBER')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('galleries.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Unggah Foto ke Galeri</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Bisa memilih beberapa foto sekaligus dalam satu kali unggah.</p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8"
            x-data="{ previews: [] }">
            <form action="{{ route('galleries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Pilih Foto</label>
                    <input type="file" name="images[]" multiple required accept="image/png, image/jpeg, image/webp"
                        @change="previews = Array.from($event.target.files).map(f => URL.createObjectURL(f))"
                        class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                    <p class="mt-1 text-xs text-slate-400">Format JPG/PNG/WEBP, maksimal 3MB per foto.</p>
                    @error('images')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="previews.length" x-cloak class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                    <template x-for="(src, i) in previews" :key="i">
                        <img :src="src" class="aspect-square w-full rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                    </template>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Judul (Opsional)</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Rapat Pleno Agustus 2026"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    <p class="mt-1 text-xs text-slate-400">Akan dipakai sebagai keterangan untuk semua foto yang diunggah sekaligus ini.</p>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="2"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('description') }}</textarea>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <a href="{{ route('galleries.index') }}"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Unggah
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
