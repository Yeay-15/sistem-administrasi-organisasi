@extends('layouts.app')

@section('title', 'Kelola Galeri - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kelola Galeri Kegiatan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Dokumentasi visual kegiatan yang akan tampil di portal publik.</p>
        </div>
        <a href="{{ route('galleries.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Unggah Foto
        </a>
    </div>

    @if ($galleries->isEmpty())
        <div class="theme-transition flex flex-col items-center gap-2 rounded-2xl border border-slate-100 bg-white p-16 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-300 dark:text-slate-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
            </svg>
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada foto di galeri.</p>
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($galleries as $item)
                <div class="theme-transition group relative overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title ?? 'Foto kegiatan' }}"
                        class="aspect-square w-full object-cover">
                    <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/70 via-black/0 to-black/0 p-3 opacity-0 transition group-hover:opacity-100">
                        @if ($item->title)
                            <p class="truncate text-xs font-semibold text-white">{{ $item->title }}</p>
                        @endif
                        <div class="mt-2 flex items-center gap-1.5">
                            <a href="{{ route('galleries.edit', $item->id) }}" title="Edit keterangan"
                                class="rounded-lg bg-white/90 p-1.5 text-amber-600 transition hover:bg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                </svg>
                            </a>
                            <form action="{{ route('galleries.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini dari galeri?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus" class="rounded-lg bg-white/90 p-1.5 text-red-600 transition hover:bg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($galleries->hasPages())
            <div class="mt-6">
                {{ $galleries->links() }}
            </div>
        @endif
    @endif
@endsection
