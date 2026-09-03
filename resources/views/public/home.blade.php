@extends('layouts.public')

@section('title', 'KATIBER - Keluarga Mahasiswa Tebing Tinggi Bersatu')
@section('meta_description', 'Portal resmi KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu, wadah silaturahmi dan pengembangan diri mahasiswa asal Tebing Tinggi di Lhokseumawe, Aceh Utara.')

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950">
        @if ($settings->hero_image_url)
            <div class="absolute inset-0">
                <img src="{{ $settings->hero_image_url }}" alt="{{ $settings->hero_title ?? 'KATIBER' }}"
                    class="h-full w-full object-cover opacity-25">
                <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/40 to-navy-950/10"></div>
            </div>
        @else
            <div class="navy-dot-pattern absolute inset-0"></div>
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-navy-600/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 left-0 h-96 w-96 rounded-full bg-amber-500/10 blur-3xl"></div>
        @endif
        <div class="relative mx-auto max-w-6xl px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
            <div class="reveal max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-white ring-1 ring-inset ring-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    Lhokseumawe, Aceh Utara
                </span>
                <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $settings->hero_title ?? 'Keluarga Mahasiswa Tebing Tinggi Bersatu' }}
                </h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-navy-100 sm:text-lg">
                    {{ $settings->hero_subtitle ?? 'Wadah silaturahmi, pengembangan diri, dan kekeluargaan bagi mahasiswa asal Tebing Tinggi yang menempuh pendidikan di Lhokseumawe.' }}
                </p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('public.about') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-amber-400 px-6 py-3 text-sm font-bold text-navy-950 shadow-lg shadow-amber-400/20 transition hover:-translate-y-0.5 hover:bg-amber-300 hover:shadow-xl hover:shadow-amber-400/30">
                        Kenali Kami
                    </a>
                    <a href="{{ route('public.news.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/25 px-6 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-white/10">
                        Baca Berita Terbaru
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        {{-- transisi halus ke bagian statistik --}}
        <div class="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-b from-transparent to-white dark:to-slate-950"></div>
    </section>

    {{-- ============ STATISTIK SINGKAT ============ --}}
    <section class="relative border-b border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-950">
        <div class="mx-auto grid max-w-4xl grid-cols-1 gap-6 px-4 py-12 sm:grid-cols-3 sm:px-6 lg:px-8">
            <div class="reveal text-center">
                <p class="text-4xl font-extrabold text-navy-800 dark:text-navy-400">{{ $totalMembers }}</p>
                <p class="mt-1.5 text-sm font-medium text-slate-500 dark:text-slate-400">Pengurus Aktif</p>
            </div>
            <div class="reveal text-center" style="--reveal-delay:100ms">
                <p class="text-4xl font-extrabold text-navy-800 dark:text-navy-400">Lhokseumawe</p>
                <p class="mt-1.5 text-sm font-medium text-slate-500 dark:text-slate-400">Aceh Utara</p>
            </div>
            <div class="reveal text-center" style="--reveal-delay:200ms">
                <p class="text-4xl font-extrabold text-navy-800 dark:text-navy-400">2014</p>
                <p class="mt-1.5 text-sm font-medium text-slate-500 dark:text-slate-400">Berdiri Sejak</p>
            </div>
        </div>
    </section>

    {{-- ============ SAMBUTAN KETUA UMUM ============ --}}
    @if ($settings->chairman_message || $settings->chairman_photo_url)
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <div
                class="reveal theme-transition relative grid grid-cols-1 items-center gap-10 overflow-hidden rounded-3xl border border-slate-100 bg-slate-50 p-8 dark:border-slate-800 dark:bg-slate-900/40 sm:p-12 lg:grid-cols-5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="pointer-events-none absolute -right-6 -top-6 h-40 w-40 text-navy-700/5 dark:text-navy-400/5">
                    <path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z" />
                </svg>
                <div class="relative flex flex-col items-center text-center lg:col-span-2">
                    <div
                        class="h-40 w-40 overflow-hidden rounded-full bg-slate-200 shadow-lg ring-4 ring-white sm:h-44 sm:w-44 dark:bg-slate-800 dark:ring-slate-900">
                        @if ($settings->chairman_photo_url)
                            <img src="{{ $settings->chairman_photo_url }}" loading="lazy"
                                alt="{{ $settings->chairman_name ?? 'Ketua Umum' }}" class="h-full w-full object-cover">
                        @else
                            <div
                                class="flex h-full w-full items-center justify-center text-4xl font-bold text-slate-400 dark:text-slate-600">
                                {{ strtoupper(substr($settings->chairman_name ?? 'K', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    @if ($settings->chairman_name)
                        <p class="mt-5 text-lg font-bold text-slate-800 dark:text-white">{{ $settings->chairman_name }}
                        </p>
                        <p class="text-sm font-semibold text-navy-700 dark:text-navy-400">Ketua Umum KATIBER</p>
                    @endif
                </div>
                <div class="relative lg:col-span-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-navy-700 dark:text-navy-400">Sambutan
                        Ketua Umum</p>
                    <p class="mt-4 text-xl italic leading-relaxed text-slate-700 dark:text-slate-200">
                        &ldquo;{{ $settings->chairman_message ?? 'Selamat datang di Portal Resmi KATIBER. Mari bersama membangun kekeluargaan dan prestasi untuk mahasiswa Tebing Tinggi.' }}&rdquo;
                    </p>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ PRESTASI MAHASISWA ============ --}}
    @if ($achievements->isNotEmpty())
        <section class="border-y border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="reveal mb-10 text-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-navy-700 dark:text-navy-400">Kebanggaan Kami</span>
                    <h2 class="mt-1.5 text-2xl font-extrabold text-slate-800 dark:text-white sm:text-3xl">Prestasi Mahasiswa</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">Kebanggaan yang ditorehkan oleh mahasiswa dan
                        pengurus KATIBER Lhokseumawe - Aceh Utara</p>
                </div>

                <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($achievements as $achievement)
                        <div class="reveal" style="--reveal-delay: {{ $loop->index * 60 }}ms">
                            <div
                                class="theme-transition group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 transition duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-navy-900/10 dark:bg-slate-900 dark:ring-slate-800">
                                <div class="absolute left-0 top-0 z-10 rounded-br-xl bg-gradient-to-r from-amber-400 to-amber-500 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-navy-950">
                                    Juara
                                </div>
                                <div class="aspect-square w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                                    @if ($achievement->photo_url)
                                        <img src="{{ $achievement->photo_url }}" alt="{{ $achievement->title }}" loading="lazy"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    @else
                                        <div
                                            class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.006 0H9.497m5.006 0a4.5 4.5 0 01-5.006 0M4.5 9V4.875C4.5 4.392 4.892 4 5.375 4h13.25c.483 0 .875.392.875.875V9m-14.5 0a3 3 0 003 3h.5m-3.5-3a3 3 0 013-3h.5M4.5 9c0 2.313 1.5 4.5 3.75 5.375M19.5 9c0 2.313-1.5 4.5-3.75 5.375M19.5 9a3 3 0 01-3 3h-.5m3.5-3a3 3 0 00-3-3h-.5" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3.5 text-center sm:p-4">
                                    <p class="line-clamp-2 text-sm font-bold text-navy-800 dark:text-navy-400">
                                        {{ $achievement->title }}</p>
                                    <p class="mt-1 truncate text-xs font-medium text-slate-700 dark:text-slate-200">
                                        {{ $achievement->achiever_name }}</p>
                                    @if ($achievement->description)
                                        <p
                                            class="mt-0.5 truncate text-[11px] uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                            {{ $achievement->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ LAPORAN KEGIATAN TERBARU ============ --}}
    @if ($latestReports->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="reveal mb-8 flex items-end justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-navy-700 dark:text-navy-400">Rekam Jejak</span>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-800 dark:text-white">Laporan Kegiatan Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Dokumentasi kegiatan KATIBER yang telah terlaksana.</p>
                </div>
                <a href="{{ route('public.reports.index') }}"
                    class="hidden shrink-0 text-sm font-semibold text-navy-700 hover:underline dark:text-navy-400 sm:inline-flex">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($latestReports as $post)
                    <a href="{{ route('public.reports.show', $post->slug) }}" class="reveal group block"
                        style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <div
                            class="theme-transition h-full overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                            <div class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                                @if ($post->cover_path)
                                    <img src="{{ asset('storage/' . $post->cover_path) }}" alt="{{ $post->title }}" loading="lazy"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                                    <span
                                        class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 font-medium text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Kegiatan</span>
                                    <span>{{ $post->published_at?->translatedFormat('d F Y') }}</span>
                                </div>
                                <h3
                                    class="mt-1.5 line-clamp-2 text-base font-bold text-slate-800 transition group-hover:text-navy-700 dark:text-white dark:group-hover:text-navy-400">
                                    {{ $post->title }}
                                </h3>
                                @if ($post->excerpt)
                                    <p class="mt-2 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">
                                        {{ $post->excerpt }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ ARTIKEL & BERITA TERBARU ============ --}}
    @if ($latestArticles->isNotEmpty())
        <section class="border-t border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="reveal mb-8 flex items-end justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-navy-700 dark:text-navy-400">Suara Mahasiswa</span>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-800 dark:text-white">Artikel & Berita Terkini</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Opini, tips, dan kabar seputar KATIBER.</p>
                    </div>
                    <a href="{{ route('public.news.index') }}"
                        class="hidden shrink-0 text-sm font-semibold text-navy-700 hover:underline dark:text-navy-400 sm:inline-flex">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($latestArticles as $post)
                        <a href="{{ route('public.news.show', $post->slug) }}" class="reveal group block"
                            style="--reveal-delay: {{ $loop->index * 80 }}ms">
                            <div
                                class="theme-transition h-full overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                                <div class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                                    @if ($post->cover_path)
                                        <img src="{{ asset('storage/' . $post->cover_path) }}" alt="{{ $post->title }}" loading="lazy"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                                        <span
                                            class="inline-flex items-center rounded-full bg-navy-50 px-2 py-0.5 font-medium text-navy-700 dark:bg-navy-500/10 dark:text-navy-400">Artikel</span>
                                        <span>{{ $post->published_at?->translatedFormat('d F Y') }}</span>
                                    </div>
                                    <h3
                                        class="mt-1.5 line-clamp-2 text-base font-bold text-slate-800 transition group-hover:text-navy-700 dark:text-white dark:group-hover:text-navy-400">
                                        {{ $post->title }}
                                    </h3>
                                    @if ($post->excerpt)
                                        <p class="mt-2 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $post->excerpt }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ AGENDA TERDEKAT ============ --}}
    @if ($upcomingAgendas->isNotEmpty())
        <section class="border-t border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-950">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="reveal mb-8 flex items-end justify-between">
                    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Agenda Terdekat</h2>
                    <a href="{{ route('public.agenda.index') }}"
                        class="hidden shrink-0 text-sm font-semibold text-navy-700 hover:underline dark:text-navy-400 sm:inline-flex">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($upcomingAgendas as $agenda)
                        <div class="reveal" style="--reveal-delay: {{ $loop->index * 80 }}ms">
                            <div
                                class="theme-transition flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                                <div
                                    class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400">
                                    <span
                                        class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($agenda->date)->format('d') }}</span>
                                    <span
                                        class="text-[11px] uppercase leading-none">{{ \Carbon\Carbon::parse($agenda->date)->translatedFormat('M') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">
                                        {{ $agenda->name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $agenda->type }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
