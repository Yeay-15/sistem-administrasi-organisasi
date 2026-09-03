@extends('layouts.public')

@section('title', 'Galeri Kegiatan - KATIBER')
@section('meta_description', 'Dokumentasi visual kegiatan dan momen kebersamaan KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu.')

@section('content')
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950">
        <div class="navy-dot-pattern absolute inset-0"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <h1 class="reveal text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Galeri Kegiatan</h1>
            <p class="reveal mt-2 text-sm text-navy-100" style="--reveal-delay:80ms">Dokumentasi visual kegiatan dan momen
                kebersamaan KATIBER Lhokseumawe-Aceh Utara.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8" x-data="{ active: null }">
        @if ($galleries->isEmpty())
            <p class="text-center text-sm text-slate-400 dark:text-slate-500">Belum ada foto di galeri.</p>
        @else
            {{-- Grid masonry menggunakan CSS columns — foto mengalir otomatis mengikuti tinggi aslinya --}}
            <div class="columns-2 gap-4 sm:columns-3 lg:columns-4 [&>*]:mb-4">
                @foreach ($galleries as $item)
                    <button type="button" @click="active = @js([
    'src' => asset('storage/' . $item->image_path),
    'title' => $item->title,
    'description' => $item->description,
])"
                        class="reveal theme-transition group block w-full overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900"
                        style="--reveal-delay: {{ ($loop->index % 8) * 50 }}ms">
                        <img src="{{ asset('storage/' . $item->image_path) }}"
                            alt="{{ $item->title ?? 'Foto kegiatan KATIBER' }}" loading="lazy"
                            class="w-full object-cover transition duration-300 group-hover:scale-105">
                        @if ($item->title)
                            <p class="truncate px-3 py-2 text-left text-xs font-medium text-slate-600 dark:text-slate-300">
                                {{ $item->title }}</p>
                        @endif
                    </button>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $galleries->links() }}
            </div>

            {{-- Lightbox sederhana untuk melihat foto ukuran penuh --}}
            <div x-show="active" x-cloak x-transition.opacity @click.self="active = null"
                @keydown.escape.window="active = null"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4">
                <button type="button" @click="active = null"
                    class="absolute right-5 top-5 rounded-lg p-2 text-white/80 transition hover:bg-white/10 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                        stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="max-h-[85vh] max-w-3xl" @click.stop>
                    <img :src="active?.src" class="max-h-[75vh] w-full rounded-xl object-contain">
                    <div x-show="active?.title || active?.description" class="mt-3 text-center">
                        <p x-show="active?.title" x-text="active?.title" class="text-sm font-semibold text-white"></p>
                        <p x-show="active?.description" x-text="active?.description" class="mt-1 text-xs text-white/70"></p>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
