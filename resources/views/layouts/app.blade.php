<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin KATIBER')</title>

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

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 min-h-screen theme-transition"
    x-data="{
        sidebarOpen: false,
        userMenuOpen: false,
        dark: document.documentElement.classList.contains('dark'),
        toggleDark() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('katiber-theme', this.dark ? 'dark' : 'light');
            document.dispatchEvent(new CustomEvent('katiber-theme-changed'));
        }
    }">

    <div class="lg:flex lg:min-h-screen">

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" x-transition.opacity
            class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 flex-col border-r border-slate-200 bg-white transition-transform duration-200 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 dark:border-slate-800 dark:bg-slate-900 theme-transition">

            <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-200 px-6 dark:border-slate-800">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-sm">
                    K
                </div>
                <div class="leading-tight">
                    <p class="text-sm font-bold text-slate-800 dark:text-white">Sistem KATIBER</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Panel Administrasi</p>
                </div>
                <button @click="sidebarOpen = false" class="ml-auto p-1 text-slate-400 hover:text-slate-600 lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="sidebar-scroll flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @can('view_dashboard')
                    @include('layouts.partials.nav-item', [
                        'route' => 'dashboard',
                        'pattern' => 'dashboard',
                        'label' => 'Dashboard',
                        'icon' => 'home',
                    ])
                @endcan
                @can('view_divisions')
                    @include('layouts.partials.nav-item', [
                        'route' => 'divisions.index',
                        'pattern' => 'divisions.*',
                        'label' => 'Divisi',
                        'icon' => 'office',
                    ])
                @endcan
                @can('view_members')
                    @include('layouts.partials.nav-item', [
                        'route' => 'members.index',
                        'pattern' => 'members.*',
                        'label' => 'Pengurus',
                        'icon' => 'users',
                    ])
                @endcan
                @can('view_agendas')
                    @include('layouts.partials.nav-item', [
                        'route' => 'agendas.index',
                        'pattern' => 'agendas.*',
                        'label' => 'Input Agenda',
                        'icon' => 'calendar',
                    ])
                @endcan
                @can('manage_attendances')
                    @include('layouts.partials.nav-item', [
                        'route' => 'attendance-reports.index',
                        'pattern' => 'attendance-reports.index',
                        'label' => 'Rekap Absensi',
                        'icon' => 'clipboard',
                    ])
                @endcan
                @can('view_guests')
                    @include('layouts.partials.nav-item', [
                        'route' => 'guests.index',
                        'pattern' => 'guests.*',
                        'label' => 'Buku Tamu',
                        'icon' => 'book',
                    ])
                @endcan
                @can('view_guidances')
                    @include('layouts.partials.nav-item', [
                        'route' => 'guidances.index',
                        'pattern' => 'guidances.*',
                        'label' => 'Pembinaan',
                        'icon' => 'academic',
                    ])
                @endcan

                @canany(['manage_news', 'manage_gallery'])
                    <p
                        class="px-3 pb-1 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">
                        Konten</p>
                @endcanany
                @can('manage_news')
                    @include('layouts.partials.nav-item', [
                        'route' => 'posts.index',
                        'pattern' => 'posts.*',
                        'label' => 'Berita',
                        'icon' => 'newspaper',
                    ])
                @endcan
                @can('manage_gallery')
                    @include('layouts.partials.nav-item', [
                        'route' => 'galleries.index',
                        'pattern' => 'galleries.*',
                        'label' => 'Galeri',
                        'icon' => 'photo',
                    ])
                @endcan
                @can('view_achievements')
                    @include('layouts.partials.nav-item', [
                        'route' => 'achievements.index',
                        'pattern' => 'achievements.*',
                        'label' => 'Prestasi',
                        'icon' => 'trophy',
                    ])
                @endcan
                @can('manage_settings')
                    @include('layouts.partials.nav-item', [
                        'route' => 'settings.edit',
                        'pattern' => 'settings.*',
                        'label' => 'Pengaturan Beranda',
                        'icon' => 'sliders',
                    ])
                @endcan
                @can('view_aspirations')
                    @include('layouts.partials.nav-item', [
                        'route' => 'aspirations.index',
                        'pattern' => 'aspirations.*',
                        'label' => 'Aspirasi Mahasiswa',
                        'icon' => 'chat',
                    ])
                @endcan

                @canany(['view_incoming_letters', 'view_outgoing_letters'])
                    <p
                        class="px-3 pb-1 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">
                        Persuratan</p>
                @endcanany
                @can('view_incoming_letters')
                    @include('layouts.partials.nav-item', [
                        'route' => 'incoming-letters.index',
                        'pattern' => 'incoming-letters.*',
                        'label' => 'Surat Masuk',
                        'icon' => 'inbox',
                    ])
                @endcan
                @can('view_outgoing_letters')
                    @include('layouts.partials.nav-item', [
                        'route' => 'outgoing-letters.index',
                        'pattern' => 'outgoing-letters.*',
                        'label' => 'Surat Keluar',
                        'icon' => 'send',
                    ])
                @endcan

                @canany(['view_audit_logs', 'manage_roles'])
                    <p
                        class="px-3 pb-1 pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">
                        Sistem</p>
                @endcanany
                @can('view_audit_logs')
                    @include('layouts.partials.nav-item', [
                        'route' => 'audit-logs.index',
                        'pattern' => 'audit-logs.index',
                        'label' => 'Audit Log',
                        'icon' => 'shield',
                    ])
                @endcan
                @can('manage_roles')
                    @include('layouts.partials.nav-item', [
                        'route' => 'roles-management.index',
                        'pattern' => 'roles-management.*',
                        'label' => 'Peran & Akses',
                        'icon' => 'key',
                    ])
                @endcan
            </nav>

            <div class="shrink-0 border-t border-slate-200 p-3 dark:border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-red-500 transition hover:bg-red-50 dark:hover:bg-red-500/10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="h-5 w-5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">

            <header
                class="sticky top-0 z-20 flex h-16 shrink-0 items-center justify-between gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur md:px-8 dark:border-slate-800 dark:bg-slate-900/80 theme-transition">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = true"
                        class="-ml-2 rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden dark:text-slate-400 dark:hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                            stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="truncate text-lg font-semibold text-slate-800 dark:text-white">@yield('title', 'Dashboard')</h1>
                </div>

                <div class="flex shrink-0 items-center gap-1.5 md:gap-3">
                    <button @click="toggleDark()"
                        class="rounded-lg p-2.5 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                        :aria-label="dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
                        title="Ganti mode gelap/terang">
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                        <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    </button>

                    <div class="hidden h-6 w-px bg-slate-200 sm:block dark:bg-slate-700"></div>

                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false"
                            class="flex items-center gap-2 rounded-lg p-1.5 pr-2 transition hover:bg-slate-100 sm:pr-3 dark:hover:bg-slate-800">
                            @if (Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar"
                                    class="h-8 w-8 shrink-0 rounded-full object-cover">
                            @else
                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                            <span
                                class="hidden max-w-[120px] truncate text-sm font-medium text-slate-700 sm:block dark:text-slate-200">{{ Auth::user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.75" stroke="currentColor"
                                class="hidden h-4 w-4 text-slate-400 sm:block">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div x-show="userMenuOpen" x-cloak x-transition.origin.top.right
                            class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                            <div class="border-b border-slate-100 px-3.5 py-2.5 dark:border-slate-700">
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">
                                    {{ Auth::user()->name }}</p>
                                <p class="truncate text-xs text-slate-400 dark:text-slate-500">
                                    {{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-2 px-2.5 py-2 mx-1.5 mt-1.5 rounded-lg text-sm text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700/60">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Pengaturan Akun
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="px-1.5 pt-1.5">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-sm text-red-500 transition hover:bg-red-50 dark:hover:bg-red-500/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H2.25" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-[1600px] flex-1 p-4 md:p-8">
                @if (session('success'))
                    <div
                        class="theme-transition mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800/60 dark:bg-green-500/10 dark:text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.75" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="theme-transition mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800/60 dark:bg-red-500/10 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.75" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
