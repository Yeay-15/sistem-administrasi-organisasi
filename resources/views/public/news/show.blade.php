@extends('layouts.public')

@section('title', $post->title . ' - KATIBER')
@section('meta_description', $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 160))

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('public.news.index') }}"
            class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Berita
        </a>

        <h1 class="text-2xl font-bold leading-tight text-slate-800 dark:text-white sm:text-3xl">{{ $post->title }}</h1>

        <div class="mt-4 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <span>Ditulis oleh: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $post->author->name ?? 'Tim KATIBER' }}</span></span>
            <span>&middot;</span>
            <span>{{ $post->published_at?->translatedFormat('d F Y') }}</span>
        </div>

        @if ($post->cover_path)
            <img src="{{ asset('storage/' . $post->cover_path) }}" alt="{{ $post->title }}"
                class="mt-6 aspect-video w-full rounded-2xl object-cover">
        @endif

        <div class="katiber-prose mt-8">
            {!! $post->content !!}
        </div>
    </article>

    @if ($relatedPosts->isNotEmpty())
        <section class="border-t border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <h2 class="mb-6 text-xl font-bold text-slate-800 dark:text-white">Berita Lainnya</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    @foreach ($relatedPosts as $related)
                        <a href="{{ route('public.news.show', $related->slug) }}"
                            class="theme-transition group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                            <div class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                                @if ($related->cover_path)
                                    <img src="{{ asset('storage/' . $related->cover_path) }}" alt="{{ $related->title }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="line-clamp-2 text-sm font-bold text-slate-800 transition group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">
                                    {{ $related->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
