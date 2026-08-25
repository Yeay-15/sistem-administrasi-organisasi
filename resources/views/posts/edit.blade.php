@extends('layouts.app')

@section('title', 'Edit Berita - KATIBER')

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
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Edit Berita</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Ditulis oleh: {{ $post->author->name ?? '-' }}
                    @if ($post->published_at)
                        &middot; Terbit {{ $post->published_at->translatedFormat('d F Y') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8"
            x-data="postForm()">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" @submit="beforeSubmit">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Berita</label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    @error('title')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Ringkasan (Opsional)</label>
                    <textarea name="excerpt" rows="2" maxlength="500"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Gambar Sampul (Opsional)</label>
                    <div class="flex items-center gap-4">
                        <div class="flex h-20 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <template x-if="coverPreview && !coverRemoved">
                                <img :src="coverPreview" alt="Preview sampul" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!coverPreview || coverRemoved">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300 dark:text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="cover" accept="image/png, image/jpeg, image/webp"
                                @change="if ($event.target.files.length) { coverPreview = URL.createObjectURL($event.target.files[0]); coverRemoved = false }"
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                            <p class="mt-1 text-xs text-slate-400">Kosongkan jika tidak ingin mengganti sampul. Maks. 2MB.</p>
                            @error('cover')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            @if ($post->cover_path)
                                <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                    <input type="checkbox" name="remove_cover" value="1" x-model="coverRemoved"
                                        class="rounded border-slate-300 text-red-600 focus:ring-red-500 dark:border-slate-600 dark:bg-slate-800">
                                    Hapus sampul saat ini
                                </label>
                            @endif
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
                        {{ $post->status === 'published' ? 'Perbarui' : 'Terbitkan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        function postForm() {
            return {
                coverPreview: @js($post->cover_path ? asset('storage/' . $post->cover_path) : null),
                coverRemoved: false,
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
                    this.quill.root.innerHTML = @js(old('content', $post->content));
                },
                beforeSubmit() {
                    this.$refs.contentInput.value = this.quill.root.innerHTML;
                },
            };
        }
    </script>
@endsection
