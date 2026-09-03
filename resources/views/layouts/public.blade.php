<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KATIBER - Keluarga Mahasiswa Tebing Tinggi Bersatu')</title>
    <meta name="description" content="@yield('meta_description', 'Portal resmi KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu, Lhokseumawe, Aceh Utara.')">

    {{-- ============ FAVICON DINAMIS ============
         Otomatis pakai logo dari Pengaturan Beranda kalau sudah diunggah,
         supaya tab browser & bookmark ikut menampilkan logo KATIBER tanpa
         perlu ganti file favicon.ico secara manual. --}}
    <link rel="icon" href="{{ $homeSettings->logo_url ?? asset('favicon.ico') }}">

    {{-- ============ OPEN GRAPH & TWITTER CARD ============
         Supaya saat tautan halaman ini dibagikan ke WhatsApp/Instagram/X,
         muncul gambar thumbnail + judul + deskripsi, bukan cuma teks polos.
         Tiap halaman bisa menimpa og_title/og_image lewat @section, kalau
         tidak diisi maka jatuh ke nilai bawaan di bawah ini. --}}
    @php
        $ogImageFallback = $homeSettings->hero_image_url ?? $homeSettings->logo_url ?? null;
    @endphp
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="KATIBER">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'KATIBER - Keluarga Mahasiswa Tebing Tinggi Bersatu')">
    <meta property="og:description" content="@yield('meta_description', 'Portal resmi KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu, Lhokseumawe, Aceh Utara.')">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @elseif ($ogImageFallback)
        <meta property="og:image" content="{{ $ogImageFallback }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <script>
        (function() {
            var stored = localStorage.getItem('katiber-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="theme-transition min-h-screen bg-white text-slate-800 dark:bg-slate-950 dark:text-slate-200"
    x-data="{
        mobileOpen: false,
        dark: document.documentElement.classList.contains('dark'),
        toggleDark() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('katiber-theme', this.dark ? 'dark' : 'light');
        }
    }">

    {{-- ============ NAVBAR ============ --}}
    <header
        class="theme-transition sticky top-0 z-40 border-b border-slate-100 bg-white/85 backdrop-blur dark:border-slate-800 dark:bg-slate-950/85">
        <nav class="mx-auto flex h-18 max-w-6xl items-center justify-between px-4 py-2.5 sm:px-6 lg:px-8">
            {{-- Logo — pojok kiri atas, ambil dari Pengaturan Beranda bila sudah diunggah --}}
            <a href="{{ route('public.home') }}" class="flex items-center gap-3">
                @if ($homeSettings->logo_url ?? null)
                    <img src="{{ $homeSettings->logo_url }}" alt="Logo KATIBER"
                        class="h-11 w-11 shrink-0 rounded-xl object-contain">
                @else
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-navy-700 to-navy-950 text-base font-bold text-white shadow-sm">
                        K
                    </div>
                @endif
                <span class="leading-tight">
                    <span
                        class="block text-sm font-extrabold tracking-tight text-slate-800 dark:text-white">KATIBER</span>
                    <span
                        class="block text-[10.5px] font-semibold uppercase tracking-wider text-navy-600 dark:text-navy-400">Lhokseumawe
                        &ndash; Aceh Utara</span>
                </span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('public.home') }}"
                    class="rounded-lg px-3.5 py-2 text-sm font-medium transition
                        {{ request()->routeIs('public.home')
                            ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                    Beranda
                </a>

                {{-- Dropdown: Profil (terbuka saat hover, dengan animasi halus) --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    @click.outside="open = false">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-1 rounded-lg px-3.5 py-2 text-sm font-medium transition
                            {{ request()->routeIs('public.about*')
                                ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        Profil
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="h-3.5 w-3.5 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                        class="absolute left-0 z-50 mt-1 w-56 origin-top rounded-xl border border-slate-100 bg-white py-1.5 shadow-lg shadow-navy-950/5 dark:border-slate-800 dark:bg-slate-900">
                        <a href="{{ route('public.about') }}"
                            class="mx-1.5 flex items-center rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('public.about') ? 'bg-navy-50 font-semibold text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            Tentang Kami
                        </a>
                        <a href="{{ route('public.about.vision') }}"
                            class="mx-1.5 flex items-center rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('public.about.vision') ? 'bg-navy-50 font-semibold text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            Visi & Misi
                        </a>
                        <a href="{{ route('public.about.structure') }}"
                            class="mx-1.5 flex items-center rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('public.about.structure') ? 'bg-navy-50 font-semibold text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            Struktur Pengurus
                        </a>
                    </div>
                </div>

                <a href="{{ route('public.agenda.index') }}"
                    class="rounded-lg px-3.5 py-2 text-sm font-medium transition
                        {{ request()->routeIs('public.agenda.*')
                            ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                    Agenda Kegiatan
                </a>

                {{-- Dropdown: Media (terbuka saat hover, dengan animasi halus) --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
                    @click.outside="open = false">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-1 rounded-lg px-3.5 py-2 text-sm font-medium transition
                            {{ request()->routeIs('public.news.*') ||
                            request()->routeIs('public.reports.*') ||
                            request()->routeIs('public.gallery')
                                ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        Media
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="h-3.5 w-3.5 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                        class="absolute left-0 z-50 mt-1 w-56 origin-top rounded-xl border border-slate-100 bg-white py-1.5 shadow-lg shadow-navy-950/5 dark:border-slate-800 dark:bg-slate-900">
                        <a href="{{ route('public.news.index') }}"
                            class="mx-1.5 flex items-center rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('public.news.*') ? 'bg-navy-50 font-semibold text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            Artikel & Berita
                        </a>
                        <a href="{{ route('public.reports.index') }}"
                            class="mx-1.5 flex items-center rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('public.reports.*') ? 'bg-navy-50 font-semibold text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            Laporan Kegiatan
                        </a>
                        <a href="{{ route('public.gallery') }}"
                            class="mx-1.5 flex items-center rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('public.gallery') ? 'bg-navy-50 font-semibold text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            Galeri
                        </a>
                    </div>
                </div>

                <a href="{{ route('public.contact') }}"
                    class="rounded-lg px-3.5 py-2 text-sm font-medium transition
                        {{ request()->routeIs('public.contact*')
                            ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                    Kontak & Aspirasi
                </a>
            </div>

            <div class="flex items-center gap-1.5">
                <button @click="toggleDark()"
                    class="rounded-lg p-2.5 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                    :aria-label="dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'" title="Ganti mode gelap/terang">
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                <a href="{{ route('login') }}"
                    class="hidden items-center gap-1.5 rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:border-navy-200 hover:bg-navy-50 hover:text-navy-800 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 sm:inline-flex">
                    Login Pengurus
                </a>

                <button @click="mobileOpen = !mobileOpen"
                    class="rounded-lg p-2.5 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 md:hidden">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        {{-- ============ MOBILE MENU ============ --}}
        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="border-t border-slate-100 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-950 md:hidden">
            <div class="flex flex-col gap-1" x-data="{ profilOpen: {{ request()->routeIs('public.about*') ? 'true' : 'false' }}, mediaOpen: {{ request()->routeIs('public.news.*') || request()->routeIs('public.reports.*') || request()->routeIs('public.gallery') ? 'true' : 'false' }} }">
                <a href="{{ route('public.home') }}"
                    class="rounded-lg px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('public.home') ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 dark:text-slate-400' }}">
                    Beranda
                </a>

                <button @click="profilOpen = !profilOpen" type="button"
                    class="flex items-center justify-between rounded-lg px-3.5 py-2.5 text-left text-sm font-medium text-slate-600 dark:text-slate-400">
                    Profil
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                        stroke="currentColor" class="h-4 w-4 transition-transform duration-200"
                        :class="profilOpen ? 'rotate-180' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="profilOpen" x-cloak x-transition class="flex flex-col gap-1 pl-4">
                    <a href="{{ route('public.about') }}"
                        class="rounded-lg px-3.5 py-2 text-sm text-slate-500 dark:text-slate-400">Tentang Kami</a>
                    <a href="{{ route('public.about.vision') }}"
                        class="rounded-lg px-3.5 py-2 text-sm text-slate-500 dark:text-slate-400">Visi & Misi</a>
                    <a href="{{ route('public.about.structure') }}"
                        class="rounded-lg px-3.5 py-2 text-sm text-slate-500 dark:text-slate-400">Struktur Pengurus</a>
                </div>

                <a href="{{ route('public.agenda.index') }}"
                    class="rounded-lg px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('public.agenda.*') ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 dark:text-slate-400' }}">
                    Agenda Kegiatan
                </a>

                <button @click="mediaOpen = !mediaOpen" type="button"
                    class="flex items-center justify-between rounded-lg px-3.5 py-2.5 text-left text-sm font-medium text-slate-600 dark:text-slate-400">
                    Media
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                        stroke="currentColor" class="h-4 w-4 transition-transform duration-200"
                        :class="mediaOpen ? 'rotate-180' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="mediaOpen" x-cloak x-transition class="flex flex-col gap-1 pl-4">
                    <a href="{{ route('public.news.index') }}"
                        class="rounded-lg px-3.5 py-2 text-sm text-slate-500 dark:text-slate-400">Artikel & Berita</a>
                    <a href="{{ route('public.reports.index') }}"
                        class="rounded-lg px-3.5 py-2 text-sm text-slate-500 dark:text-slate-400">Laporan Kegiatan</a>
                    <a href="{{ route('public.gallery') }}"
                        class="rounded-lg px-3.5 py-2 text-sm text-slate-500 dark:text-slate-400">Galeri</a>
                </div>

                <a href="{{ route('public.contact') }}"
                    class="rounded-lg px-3.5 py-2.5 text-sm font-medium transition {{ request()->routeIs('public.contact*') ? 'bg-navy-50 text-navy-800 dark:bg-navy-500/10 dark:text-navy-400' : 'text-slate-600 dark:text-slate-400' }}">
                    Kontak & Aspirasi
                </a>

                <a href="{{ route('login') }}"
                    class="mt-1 rounded-lg border border-slate-200 px-3.5 py-2.5 text-center text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">
                    Login Pengurus
                </a>
            </div>
        </div>
    </header>

    {{-- ============ KONTEN ============ --}}
    <main>
        @yield('content')
    </main>

    {{-- ============ CTA WHATSAPP (opsional, tampil jika nomor WA sudah diatur) ============
    @if ($homeSettings->whatsapp_link ?? null)
        <section class="bg-gradient-to-br from-navy-800 to-navy-950">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-5 px-4 py-9 sm:flex-row sm:px-6 lg:px-8">
                <div class="text-center sm:text-left">
                    <span class="inline-block rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-white">Info Terupdate</span>
                    <h3 class="mt-2 text-lg font-bold text-white sm:text-xl">Jangan Ketinggalan Info Kegiatan KATIBER!</h3>
                    <p class="mt-1 text-sm text-navy-100">Dapatkan info agenda, prestasi, dan kabar terbaru langsung lewat WhatsApp.</p>
                </div>
                <a href="{{ $homeSettings->whatsapp_link }}" target="_blank"
                    class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-navy-800 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.5-1.185A8.959 8.959 0 013 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                    Gabung Channel WA
                </a>
            </div>
        </section>
    @endif --}}

    {{-- ============ FOOTER ============ --}}
    <footer class="theme-transition border-t border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Kolom 1: Profil --}}
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="mb-3 flex items-center gap-2.5">
                        @if ($homeSettings->logo_url ?? null)
                            <img src="{{ $homeSettings->logo_url }}" alt="Logo KATIBER"
                                class="h-9 w-9 shrink-0 rounded-lg object-contain">
                        @else
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-navy-700 to-navy-950 text-xs font-bold text-white">
                                K
                            </div>
                        @endif
                        <span class="leading-tight">
                            <span
                                class="block text-sm font-extrabold tracking-tight text-slate-800 dark:text-white">KATIBER</span>
                            <span
                                class="block text-[10px] font-semibold uppercase tracking-wider text-navy-600 dark:text-navy-400">Lhokseumawe
                                &ndash; Aceh Utara</span>
                        </span>
                    </div>
                    <p class="max-w-sm text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        Wadah silaturahmi dan pengembangan diri mahasiswa asal Tebing Tinggi di Lhokseumawe, Aceh Utara.
                    </p>
                </div>

                {{-- Kolom 2: Jelajahi --}}
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Jelajahi</p>
                    <div class="flex flex-col gap-2.5 text-sm text-slate-500 dark:text-slate-400">
                        <a href="{{ route('public.about') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Tentang Kami</a>
                        <a href="{{ route('public.about.vision') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Visi & Misi</a>
                        <a href="{{ route('public.about.structure') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Struktur Pengurus</a>
                        <a href="{{ route('public.agenda.index') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Agenda Kegiatan</a>
                        <a href="{{ route('public.news.index') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Artikel & Berita</a>
                        <a href="{{ route('public.reports.index') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Laporan Kegiatan</a>
                        <a href="{{ route('public.gallery') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Galeri</a>
                    </div>
                </div>

                {{-- Kolom 3: Hubungi Kami --}}
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Hubungi Kami</p>
                    <div class="flex flex-col gap-3 text-sm text-slate-500 dark:text-slate-400">
                        @if ($homeSettings->whatsapp_link ?? null)
                            <a href="{{ $homeSettings->whatsapp_link }}" target="_blank"
                                class="flex items-start gap-2.5 transition hover:text-navy-700 dark:hover:text-navy-400">
                                {{-- Logo WA Asli (Kecil) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="mt-0.5 h-4 w-4 shrink-0">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                                <span>
                                    <span class="block text-xs text-slate-400 dark:text-slate-500">Chat WhatsApp</span>
                                    {{ $homeSettings->whatsapp_number }}
                                </span>
                            </a>
                        @endif
                        @if ($homeSettings->contact_email ?? null)
                            <a href="mailto:{{ $homeSettings->contact_email }}"
                                class="flex items-start gap-2.5 transition hover:text-navy-700 dark:hover:text-navy-400">
                                {{-- Logo Email Solid (Kecil) --}}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="mt-0.5 h-4 w-4 shrink-0">
                                    <path
                                        d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                                    <path
                                        d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                                </svg>
                                <span>
                                    <span class="block text-xs text-slate-400 dark:text-slate-500">Email Resmi</span>
                                    {{ $homeSettings->contact_email }}
                                </span>
                            </a>
                        @endif
                        <a href="{{ route('public.contact') }}"
                            class="transition hover:text-navy-700 dark:hover:text-navy-400">Kontak & Aspirasi
                            &rarr;</a>
                    </div>
                </div>

                {{-- Kolom 4: Ikuti Kami --}}
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        Ikuti Kami</p>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ $homeSettings->instagram_url ?? 'https://instagram.com' }}" target="_blank"
                            aria-label="Instagram KATIBER"
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:-translate-y-0.5 hover:border-navy-200 hover:bg-navy-50 hover:text-navy-700 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800">
                            {{-- Logo Instagram Asli --}}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="h-5 w-5">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                            </svg>
                        </a>
                        @if ($homeSettings->whatsapp_link ?? null)
                            <a href="{{ $homeSettings->whatsapp_link }}" target="_blank"
                                aria-label="WhatsApp KATIBER"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:-translate-y-0.5 hover:border-navy-200 hover:bg-navy-50 hover:text-navy-700 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800">
                                {{-- Logo WA Asli --}}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="h-5 w-5">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </a>
                        @endif
                        @if ($homeSettings->contact_email ?? null)
                            <a href="mailto:{{ $homeSettings->contact_email }}" aria-label="Email KATIBER"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:-translate-y-0.5 hover:border-navy-200 hover:bg-navy-50 hover:text-navy-700 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800">
                                {{-- Logo Email Solid --}}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="h-5 w-5">
                                    <path
                                        d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                                    <path
                                        d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div
                class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-slate-200 pt-6 text-center text-xs text-slate-400 dark:border-slate-800 dark:text-slate-500 sm:flex-row sm:text-left">
                <p>&copy; {{ date('Y') }} KATIBER Lhokseumawe aceh Utara. Seluruh hak cipta
                    dilindungi.</p>
                <p>Lhokseumawe, Aceh Utara</p>
            </div>
        </div>
    </footer>
</body>

</html>
