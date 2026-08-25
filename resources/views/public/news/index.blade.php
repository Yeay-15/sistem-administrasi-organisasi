@extends('layouts.public')

@section('title', 'Berita & Artikel - KATIBER')

@section('content')
    <section class="border-b border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/40">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Berita & Artikel</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Kabar, kegiatan, dan cerita seputar KATIBER.</p>

            <form method="GET" class="mt-6 max-w-md">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"
                        class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berita..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                </div>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
        @if ($posts->isEmpty())
            <p class="text-center text-sm text-slate-400 dark:text-slate-500">
                @if (request('search'))
                    Tidak ada berita yang cocok dengan pencarian "{{ request('search') }}".
                @else
                    Belum ada berita yang diterbitkan.
                @endif
            </p>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <a href="{{ route('public.news.show', $post->slug) }}"
                        class="theme-transition group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                        <div class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                            @if ($post->cover_path)
                                <img src="{{ asset('storage/' . $post->cover_path) }}" alt="{{ $post->title }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-slate-400 dark:text-slate-500">{{ $post->published_at?->translatedFormat('d F Y') }}</p>
                            <h3 class="mt-1.5 line-clamp-2 text-base font-bold text-slate-800 transition group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">
                                {{ $post->title }}
                            </h3>
                            @if ($post->excerpt)
                                <p class="mt-2 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $post->excerpt }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
@endsection
