@extends('layouts.public')

@section('title', 'Struktur Pengurus - KATIBER')

@section('content')
    <section class="border-b border-slate-100 bg-gradient-to-b from-blue-50 to-white dark:border-slate-800 dark:from-slate-900 dark:to-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white sm:text-4xl">Struktur Pengurus</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-slate-500 dark:text-slate-400">
                Mengenal lebih dekat para pengurus aktif yang menjalankan roda organisasi KATIBER.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        @forelse ($divisions as $division)
            @if ($division->members->isNotEmpty())
                <div class="mb-12">
                    <div class="mb-5 flex items-center gap-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $division->name }}</h3>
                        <span class="h-px flex-1 bg-slate-100 dark:bg-slate-800"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($division->members as $member)
                            <div class="theme-transition group rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                                <div class="mx-auto mb-3 h-20 w-20 overflow-hidden rounded-full bg-slate-100 ring-2 ring-slate-100 transition group-hover:ring-blue-200 dark:bg-slate-800 dark:ring-slate-800 dark:group-hover:ring-blue-500/30">
                                    @if ($member->photo_url)
                                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xl font-bold text-slate-400 dark:text-slate-600">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">{{ $member->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-blue-600 dark:text-blue-400">{{ $member->position }}</p>
                                @if ($member->major || $member->university)
                                    <p class="mt-1 truncate text-[11px] leading-tight text-slate-400 dark:text-slate-500">
                                        {{ $member->major }}{{ $member->major && $member->university ? ' — ' : '' }}{{ $member->university }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <p class="text-center text-sm text-slate-400 dark:text-slate-500">Data pengurus belum tersedia.</p>
        @endforelse
    </section>
@endsection
