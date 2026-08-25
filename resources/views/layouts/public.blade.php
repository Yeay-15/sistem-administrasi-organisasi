<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KATIBER - Keluarga Mahasiswa Tebing Tinggi Bersatu')</title>
    <meta name="description" content="@yield('meta_description', 'Portal resmi KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu, Lhokseumawe, Aceh Utara.')">

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
    <header class="theme-transition sticky top-0 z-40 border-b border-slate-100 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
        <nav class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('public.home') }}" class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-sm">
                    K
                </div>
                <span class="text-sm font-bold text-slate-800 dark:text-white">KATIBER</span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @php
                    $publicNav = [
                        ['route' => 'public.home', 'label' => 'Beranda'],
                        ['route' => 'public.about', 'label' => 'Tentang Kami'],
                        ['route' => 'public.news.index', 'label' => 'Berita & Artikel'],
                        ['route' => 'public.gallery', 'label' => 'Galeri Kegiatan'],
                        ['route' => 'public.contact', 'label' => 'Hubungi Kami'],
                    ];
                @endphp
                @foreach ($publicNav as $item)
                    <a href="{{ route($item['route']) }}"
                        class="rounded-lg px-3.5 py-2 text-sm font-medium transition
                            {{ request()->routeIs($item['route']) || ($item['route'] === 'public.news.index' && request()->routeIs('public.news.*'))
                                ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400'
                                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-1.5">
                <button @click="toggleDark()"
                    class="rounded-lg p-2.5 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                    :aria-label="dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'" title="Ganti mode gelap/terang">
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                <a href="{{ route('login') }}"
                    class="hidden items-center gap-1.5 rounded-lg border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 sm:inline-flex">
                    Login Pengurus
                </a>

                <button @click="mobileOpen = !mobileOpen" class="rounded-lg p-2.5 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 md:hidden">
                    <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        <div x-show="mobileOpen" x-cloak x-transition.opacity class="border-t border-slate-100 bg-white px-4 py-3 dark:border-slate-800 dark:bg-slate-950 md:hidden">
            <div class="flex flex-col gap-1">
                @foreach ($publicNav as $item)
                    <a href="{{ route($item['route']) }}"
                        class="rounded-lg px-3.5 py-2.5 text-sm font-medium transition
                            {{ request()->routeIs($item['route']) ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('login') }}" class="mt-1 rounded-lg border border-slate-200 px-3.5 py-2.5 text-center text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">
                    Login Pengurus
                </a>
            </div>
        </div>
    </header>

    {{-- ============ KONTEN ============ --}}
    <main>
        @yield('content')
    </main>

    {{-- ============ FOOTER ============ --}}
    <footer class="theme-transition border-t border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                <div>
                    <div class="mb-3 flex items-center gap-2.5">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-xs font-bold text-white">
                            K
                        </div>
                        <span class="text-sm font-bold text-slate-800 dark:text-white">KATIBER</span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                        Keluarga Mahasiswa Tebing Tinggi Bersatu — wadah silaturahmi dan pengembangan diri mahasiswa asal Tebing Tinggi di Lhokseumawe, Aceh Utara.
                    </p>
                </div>
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Navigasi</p>
                    <div class="flex flex-col gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <a href="{{ route('public.about') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">Tentang Kami</a>
                        <a href="{{ route('public.news.index') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">Berita & Artikel</a>
                        <a href="{{ route('public.gallery') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">Galeri Kegiatan</a>
                        <a href="{{ route('public.contact') }}" class="transition hover:text-blue-600 dark:hover:text-blue-400">Hubungi Kami</a>
                    </div>
                </div>
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Sekretariat</p>
                    <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">Lhokseumawe, Aceh Utara, Indonesia</p>
                </div>
            </div>

            <div class="mt-10 border-t border-slate-200 pt-6 text-center text-xs text-slate-400 dark:border-slate-800 dark:text-slate-500">
                &copy; {{ date('Y') }} KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>
</body>

</html>
