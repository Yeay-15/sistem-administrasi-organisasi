@extends('layouts.app')

@section('title', 'Edit Pengurus - KATIBER')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('members.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Edit Data Pengurus</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Perbarui data diri dan jabatan pengurus.</p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <form action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-6 flex items-center gap-5" x-data="{ preview: @js($member->photo_url), removed: false }">
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                        <template x-if="preview && !removed">
                            <img :src="preview" alt="Preview foto" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!preview || removed">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-300 dark:text-slate-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1">
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Foto Pengurus (Opsional)</label>
                        <input type="file" name="photo" accept="image/png, image/jpeg, image/webp"
                            @change="if ($event.target.files.length) { preview = URL.createObjectURL($event.target.files[0]); removed = false }"
                            class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                        <p class="mt-1 text-xs text-slate-400">Format JPG/PNG/WEBP, maksimal 2MB. Kosongkan jika tidak ingin mengganti foto.</p>
                        @error('photo')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        @if ($member->photo_url)
                            <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                <input type="checkbox" name="remove_photo" value="1" x-model="removed"
                                    class="rounded border-slate-300 text-red-600 focus:ring-red-500 dark:border-slate-600 dark:bg-slate-800">
                                Hapus foto saat ini
                            </label>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">NIM</label>
                        <input type="text" name="student_id" value="{{ old('student_id', $member->student_id) }}" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('student_id')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Angkatan</label>
                        <input type="text" name="batch" value="{{ old('batch', $member->batch) }}" placeholder="Contoh: 2024" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('batch')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Divisi</label>
                        <select name="division_id" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id', $member->division_id) == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jabatan</label>
                        <select name="position" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach (['Ketua Umum', 'Sekretaris Umum', 'Bendahara Umum', 'Ketua Divisi', 'Sekretaris Divisi', 'Anggota Divisi'] as $jabatan)
                                <option value="{{ $jabatan }}" {{ old('position', $member->position) == $jabatan ? 'selected' : '' }}>
                                    {{ $jabatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('position')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jurusan (Opsional)</label>
                        <input type="text" name="major" value="{{ old('major', $member->major) }}" placeholder="Contoh: Teknik Informatika"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('major')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Universitas (Opsional)</label>
                        <input type="text" name="university" value="{{ old('university', $member->university) }}" placeholder="Contoh: Universitas Malikussaleh"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('university')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Masuk</label>
                        <input type="date" name="join_date" value="{{ old('join_date', $member->join_date) }}" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('join_date')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                        <select name="status" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="Aktif" {{ old('status', $member->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ old('status', $member->status) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Keterangan (Opsional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('notes', $member->notes) }}</textarea>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <a href="{{ route('members.index') }}"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
