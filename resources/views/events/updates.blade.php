@extends('layouts.app')

@section('title', 'Info Terkini - ' . $event->title)

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('events.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Info Terkini</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $event->title }} &bull; muncul di halaman publik sebagai timeline</p>
            </div>
        </div>

        @can('manage_events')
        <div class="theme-transition mb-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-3 text-sm font-bold text-slate-700 dark:text-slate-300">Tambah Info Baru</h2>
            <form action="{{ route('events.updates.store', $event) }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="title" required maxlength="255" placeholder="Judul, contoh: Technical Meeting Diundur ke Jumat"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                <textarea name="body" required rows="3" maxlength="3000" placeholder="Isi pengumuman..."
                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500"></textarea>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Publikasikan</button>
            </form>
        </div>
        @endcan

        <div class="space-y-3">
            @forelse ($updates as $update)
                <div class="theme-transition rounded-xl border border-slate-100 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white">{{ $update->title }}</h3>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $update->published_at?->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        @can('manage_events')
                        <form action="{{ route('events.updates.destroy', [$event, $update]) }}" method="POST"
                            onsubmit="return confirm('Hapus info ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="shrink-0 rounded p-1.5 text-slate-300 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $update->body }}</p>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-slate-400">Belum ada info terkini yang dipublikasikan.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $updates->links() }}</div>
    </div>
@endsection
