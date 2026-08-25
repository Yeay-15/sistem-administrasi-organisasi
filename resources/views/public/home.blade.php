@extends('layouts.public')

@section('title', 'KATIBER - Keluarga Mahasiswa Tebing Tinggi Bersatu')

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">
        @if ($settings->hero_image_url)
            <div class="absolute inset-0">
                <img src="{{ $settings->hero_image_url }}" alt="{{ $settings->hero_title ?? 'KATIBER' }}" class="h-full w-full object-cover opacity-25">
            </div>
        @else
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
        @endif
        <div class="relative mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="max-w-2xl">
                <span class="inline-block rounded-full bg-white/15 px-3.5 py-1 text-xs font-semibold text-white">
                    Lhokseumawe, Aceh Utara
                </span>
                <h1 class="mt-4 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                    {{ $settings->hero_title ?? 'Keluarga Mahasiswa Tebing Tinggi Bersatu' }}
                </h1>
                <p class="mt-4 text-base leading-relaxed text-blue-100 sm:text-lg">
                    {{ $settings->hero_subtitle ?? 'Wadah silaturahmi, pengembangan diri, dan kekeluargaan bagi mahasiswa asal Tebing Tinggi yang menempuh pendidikan di Lhokseumawe.' }}
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('public.about') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-50">
                        Kenali Kami
                    </a>
                    <a href="{{ route('public.news.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-white/30 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                        Baca Berita Terbaru
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ STATISTIK SINGKAT ============ --}}
    <section class="border-b border-slate-100 bg-white dark:border-slate-800 dark:bg-slate-950">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-6 px-4 py-10 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalMembers }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pengurus Aktif</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalDivisions }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Divisi</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">Lhokseumawe</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Aceh Utara</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">2024</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Berdiri Sejak</p>
            </div>
        </div>
    </section>

    {{-- ============ SAMBUTAN KETUA UMUM ============ --}}
    @if ($settings->chairman_message || $settings->chairman_photo_url)
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="theme-transition grid grid-cols-1 items-center gap-10 rounded-3xl border border-slate-100 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-900/40 sm:p-10 lg:grid-cols-3">
                <div class="flex flex-col items-center text-center lg:col-span-1">
                    <div class="h-32 w-32 overflow-hidden rounded-full bg-slate-200 ring-4 ring-white dark:bg-slate-800 dark:ring-slate-900">
                        @if ($settings->chairman_photo_url)
                            <img src="{{ $settings->chairman_photo_url }}" alt="{{ $settings->chairman_name ?? 'Ketua Umum' }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-3xl font-bold text-slate-400 dark:text-slate-600">
                                {{ strtoupper(substr($settings->chairman_name ?? 'K', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    @if ($settings->chairman_name)
                        <p class="mt-4 text-base font-bold text-slate-800 dark:text-white">{{ $settings->chairman_name }}</p>
                        <p class="text-sm text-blue-600 dark:text-blue-400">Ketua Umum KATIBER</p>
                    @endif
                </div>
                <div class="lg:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Sambutan Ketua Umum</p>
                    <p class="mt-3 text-lg italic leading-relaxed text-slate-600 dark:text-slate-300">
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
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Prestasi Mahasiswa</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kebanggaan yang ditorehkan oleh mahasiswa dan pengurus KATIBER.</p>
                </div>

                <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($achievements as $achievement)
                        <div class="theme-transition group overflow-hidden rounded-2xl border-t-4 border-amber-400 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:bg-slate-900">
                            <div class="aspect-square w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                                @if ($achievement->photo_url)
                                    <img src="{{ $achievement->photo_url }}" alt="{{ $achievement->title }}"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.006 0H9.497m5.006 0a4.5 4.5 0 01-5.006 0M4.5 9V4.875C4.5 4.392 4.892 4 5.375 4h13.25c.483 0 .875.392.875.875V9m-14.5 0a3 3 0 003 3h.5m-3.5-3a3 3 0 013-3h.5M4.5 9c0 2.313 1.5 4.5 3.75 5.375M19.5 9c0 2.313-1.5 4.5-3.75 5.375M19.5 9a3 3 0 01-3 3h-.5m3.5-3a3 3 0 00-3-3h-.5" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3.5 text-center sm:p-4">
                                <p class="line-clamp-2 text-sm font-bold text-blue-700 dark:text-blue-400">{{ $achievement->title }}</p>
                                <p class="mt-1 truncate text-xs font-medium text-slate-700 dark:text-slate-200">{{ $achievement->achiever_name }}</p>
                                @if ($achievement->description)
                                    <p class="mt-0.5 truncate text-[11px] uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $achievement->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ BERITA TERBARU ============ --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Berita Terbaru</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kabar dan kegiatan terbaru dari KATIBER.</p>
            </div>
            <a href="{{ route('public.news.index') }}" class="hidden text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400 sm:inline-flex">
                Lihat Semua &rarr;
            </a>
        </div>

        @if ($latestPosts->isEmpty())
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada berita yang diterbitkan.</p>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($latestPosts as $post)
                    @php
                        $showRoute = $post->category === 'Laporan Kegiatan' ? 'public.reports.show' : 'public.news.show';
                    @endphp
                    <a href="{{ route($showRoute, $post->slug) }}"
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
                            <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ $post->category }}</span>
                                <span>{{ $post->published_at?->translatedFormat('d F Y') }}</span>
                            </div>
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
        @endif
    </section>

    {{-- ============ AGENDA TERDEKAT ============ --}}
    @if ($upcomingAgendas->isNotEmpty())
        <section class="border-t border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/40">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="mb-8 flex items-end justify-between">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Agenda Terdekat</h2>
                    <a href="{{ route('public.agenda.index') }}" class="hidden text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400 sm:inline-flex">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($upcomingAgendas as $agenda)
                        <div class="theme-transition flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                <span class="text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($agenda->date)->format('d') }}</span>
                                <span class="text-[11px] uppercase leading-none">{{ \Carbon\Carbon::parse($agenda->date)->translatedFormat('M') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">{{ $agenda->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $agenda->type }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
