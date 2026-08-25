@extends('layouts.app')

@section('title', 'Tulis Berita - KATIBER')

@push('head') @endpush

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('posts.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Tulis Berita Baru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Isi konten, atur format teks, dan sisipkan gambar sesuai kebutuhan.</p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8"
            x-data="postForm()">
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" @submit="beforeSubmit">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Berita</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    @error('title')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Kategori</label>
                    <select name="category"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="Artikel & Berita" {{ old('category') === 'Artikel & Berita' ? 'selected' : '' }}>Artikel & Berita</option>
                        <option value="Laporan Kegiatan" {{ old('category') === 'Laporan Kegiatan' ? 'selected' : '' }}>Laporan Kegiatan</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Menentukan tampil di menu Media mana pada Portal Publik.</p>
                    @error('category')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Ringkasan (Opsional)</label>
                    <textarea name="excerpt" rows="2" maxlength="500" placeholder="Ringkasan singkat yang tampil di daftar berita portal publik."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Gambar Sampul (Opsional)</label>
                    <div class="flex items-center gap-4" x-data="{}">
                        <div class="flex h-20 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <template x-if="coverPreview">
                                <img :src="coverPreview" alt="Preview sampul" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!coverPreview">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300 dark:text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="cover" accept="image/png, image/jpeg, image/webp"
                                @change="coverPreview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                            <p class="mt-1 text-xs text-slate-400">Digunakan sebagai thumbnail di daftar berita. Maks. 2MB.</p>
                            @error('cover')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Isi Berita</label>
                    <div id="editor-container" style="min-height: 280px;" class="theme-transition rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800"></div>
                    <input type="hidden" name="content" x-ref="contentInput">
                    @error('content')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex flex-col-reverse items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800 sm:flex-row">
                    <a href="{{ route('posts.index') }}"
                        class="w-full rounded-lg border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 sm:w-auto">Batal</a>
                    <button type="submit" name="status" value="draft"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800 sm:w-auto">
                        Simpan sebagai Draf
                    </button>
                    <button type="submit" name="status" value="published"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20 sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Terbitkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        function postForm() {
            return {
                coverPreview: null,
                quill: null,
                init() {
                    this.quill = new Quill('#editor-container', {
                        theme: 'snow',
                        placeholder: 'Tulis isi berita di sini...',
                        modules: {
                            toolbar: [
                                [{ header: [2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['link', 'image'],
                                ['clean'],
                            ],
                        },
                    });
                },
                beforeSubmit() {
                    // Salin HTML hasil Quill ke input hidden sebelum form dikirim.
                    this.$refs.contentInput.value = this.quill.root.innerHTML;
                },
            };
        }
    </script>
@endsection
