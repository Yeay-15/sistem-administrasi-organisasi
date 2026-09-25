@extends('layouts.app')

@section('title', 'Tambah Anggota Non-Pengurus - KATIBER')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('non-pengurus.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Tambah Anggota Non-Pengurus</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Untuk orang yang ikut kegiatan/kepanitiaan tapi bukan bagian struktur Pengurus.</p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <form action="{{ route('non-pengurus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('name')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">NIM (opsional)</label>
                        <input type="text" name="student_id" value="{{ old('student_id') }}"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('student_id')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Asal Kampus / Instansi (opsional)</label>
                        <input type="text" name="university" value="{{ old('university') }}"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('university')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jurusan (opsional)</label>
                        <input type="text" name="major" value="{{ old('major') }}"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('major')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Foto (opsional)</label>
                        <input type="file" name="photo" accept="image/png, image/jpeg, image/webp"
                            class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                        @error('photo')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan (opsional)</label>
                        <textarea name="notes" rows="3"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('non-pengurus.index') }}"
                        class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit"
                        class="rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
