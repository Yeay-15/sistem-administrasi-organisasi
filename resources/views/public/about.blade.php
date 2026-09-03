@extends('layouts.public')

@section('title', 'Tentang Kami - KATIBER')
@section('meta_description', 'Mengenal KATIBER — sejarah, tujuan, jargon, dan estafet kepemimpinan Keluarga Mahasiswa
    Tebing Tinggi Bersatu sejak 2014.')

@section('content')

    <section class="relative overflow-hidden bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950">
        <div class="navy-dot-pattern absolute inset-0"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-20 text-center sm:px-6 lg:px-8">
            <span
                class="reveal inline-block rounded-full bg-white/10 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-white ring-1 ring-inset ring-white/15">Profil
                Organisasi</span>
            <h1 class="reveal mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-5xl" style="--reveal-delay:80ms">
                Tentang Kami</h1>
            <p class="reveal mx-auto mt-4 max-w-2xl text-base leading-relaxed text-navy-100 sm:text-lg"
                style="--reveal-delay:160ms">
                KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu — adalah organisasi berbentuk kekeluargaan yang
                menaungi mahasiswa asal Kota Tebing Tinggi dan sekitarnya yang menempuh pendidikan di wilayah
                Lhokseumawe dan Aceh Utara. Kami hadir di tanah rantau untuk menjalin persatuan serta persaudaraan
                lahiriah maupun batiniah antar sesama anggota. Lebih dari sekadar ruang silaturahmi dan pengembangan
                potensi diri, organisasi ini juga berfungsi secara utuh sebagai wadah komunikasi serta kerja sama
                dalam menampung, menyalurkan, dan melaksanakan aspirasi seluruh mahasiswa yang tergabung di
                dalamnya.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">

        {{-- ============ SEJARAH SINGKAT ============ --}}
        <div
            class="reveal theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 bg-gradient-to-r from-navy-700 to-navy-900 px-6 py-5 sm:px-10">
                <h2 class="text-lg font-bold text-white">Sejarah Singkat</h2>
            </div>
            <div class="space-y-4 px-6 py-6 text-sm leading-relaxed text-slate-600 dark:text-slate-300 sm:px-10">
                <p>
                    Keluarga Mahasiswa Tebing Tinggi Bersatu (KATIBER) Lhokseumawe-Aceh Utara adalah wadah paguyuban
                    yang menaungi mahasiswa asal Kota Tebing Tinggi dan sekitarnya yang berkuliah di Lhokseumawe atau
                    Aceh Utara. Perjalanan organisasi ini dimulai pada tahun 2013 dengan awal bernama Ikatan
                    Mahasiswa Tebing Tinggi (IMATETI), yang kemudian diresmikan secara kelembagaan pada 13 Maret 2014
                    di Kota Lhokseumawe.
                </p>
                <p>
                    Seiring bertambahnya dinamika mahasiswa dan kebutuhan akan wadah yang lebih inklusif, organisasi
                    ini melakukan reformasi struktural pada Maret 2017. Melalui musyawarah dan kesepahaman bersama,
                    nama IMATETI bertransformasi menjadi KATIBER. Transformasi ini menjadi titik tolak untuk
                    merangkul mahasiswa yang tersebar di berbagai perguruan tinggi di wilayah Lhokseumawe dan Aceh
                    Utara, termasuk Universitas Malikussaleh (Unimal), Politeknik Negeri Lhokseumawe, dan IAIN
                    Lhokseumawe.
                </p>
                <p>
                    Tonggak kematangan kelembagaan KATIBER terwujud pada tahun 2018 melalui penyelenggaraan
                    Musyawarah Besar (Mubes) yang secara resmi merumuskan Anggaran Dasar dan Anggaran Rumah Tangga
                    (AD/ART). Sejak saat itu, KATIBER berdiri di atas landasan hukum yang kuat dengan sistem
                    kepemimpinan yang demokratis.
                </p>
                <p>
                    Hingga hari ini, KATIBER terus berkembang dengan memegang teguh nilai kekeluargaan, bergerak
                    secara organik, dan menjaga jarak dari politik praktis agar senantiasa menjadi &ldquo;rumah&rdquo;
                    yang nyaman bagi setiap generasi penerusnya.
                </p>
            </div>
        </div>

        {{-- ============ TUJUAN & JARGON ============ --}}
        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-5">
            {{-- Tujuan Organisasi --}}
            <div
                class="reveal theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:col-span-3">
                <div class="border-b border-slate-100 bg-gradient-to-r from-navy-700 to-navy-900 px-6 py-5">
                    <h2 class="text-lg font-bold text-white">Tujuan Organisasi</h2>
                </div>
                <p class="px-6 py-6 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    Organisasi ini dibentuk dengan tujuan utama untuk mempererat tali persaudaraan antar sesama
                    mahasiswa yang berasal dari Kota Tebing Tinggi dan sekitarnya. Lebih dari itu, KATIBER juga hadir
                    untuk mewujudkan serta meningkatkan intelektualitas mahasiswa agar kelak mampu bertanggung jawab
                    sebagai pemimpin yang senantiasa menjunjung tinggi nilai-nilai kebenaran, keadilan, kekeluargaan,
                    dan kedisiplinan.
                </p>
            </div>

            {{-- Jargon Organisasi --}}
            <div class="reveal theme-transition relative flex flex-col justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-navy-800 to-navy-950 p-6 text-center shadow-sm sm:col-span-2"
                style="--reveal-delay:100ms">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="pointer-events-none absolute -right-4 -top-4 h-24 w-24 text-white/5">
                    <path
                        d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z" />
                </svg>
                <p class="relative text-[11px] font-bold uppercase tracking-wider text-amber-400">Jargon Organisasi</p>
                <p class="relative mt-3 text-lg font-extrabold italic leading-snug text-white">
                    &ldquo;Katiber Satu, Katiber Satu,<br>Katiber Gold Generation!&rdquo;
                </p>
            </div>
        </div>

        {{-- ============ ESTAFET KEPEMIMPINAN (TIMELINE) ============ --}}
        <div class="reveal mt-14">
            <div class="mb-8 text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-navy-700 dark:text-navy-400">Sejak
                    2014</span>
                <h2 class="mt-1.5 text-2xl font-extrabold text-slate-800 dark:text-white">Estafet Kepemimpinan</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">
                    Generasi-generasi penggerak yang telah membawa KATIBER ke arah yang lebih baik.
                </p>
            </div>

            <div class="relative">
                @if ($leaders->isEmpty())
                    <div
                        class="theme-transition rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center dark:border-slate-800 dark:bg-slate-900">
                        <p class="text-sm text-slate-400 dark:text-slate-500">Data estafet kepemimpinan belum
                            ditambahkan.</p>
                    </div>
                @else
                    {{-- Garis vertikal penghubung --}}
                    <div
                        class="absolute bottom-2 left-[27px] top-2 w-0.5 bg-gradient-to-b from-navy-700 via-navy-300 to-navy-50 dark:from-navy-500 dark:via-navy-800 dark:to-slate-900">
                    </div>

                    <div class="space-y-6">
                        @foreach ($leaders as $i => $leader)
                            <div class="reveal relative flex items-start gap-5"
                                style="--reveal-delay: {{ $i * 60 }}ms">
                                {{-- Foto Ketua Umum, dengan fallback logo/siluet bila belum ada foto --}}
                                <div
                                    class="relative z-10 h-14 w-14 shrink-0 overflow-hidden rounded-full bg-slate-100 shadow-md ring-4 ring-white dark:bg-slate-800 dark:ring-slate-950">
                                    @if ($leader->photo_url)
                                        <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" loading="lazy"
                                            class="h-full w-full object-cover">
                                    @elseif ($homeSettings->logo_url ?? null)
                                        <img src="{{ $homeSettings->logo_url }}" alt="Logo KATIBER"
                                            class="h-full w-full object-contain p-1.5">
                                    @else
                                        <div
                                            class="flex h-full w-full items-center justify-center bg-gradient-to-br from-navy-100 to-navy-200 text-navy-500 dark:from-slate-800 dark:to-slate-800 dark:text-slate-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Kartu --}}
                                <div
                                    class="theme-transition group flex-1 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 {{ $loop->last ? 'ring-1 ring-amber-300/60 dark:ring-amber-400/20' : '' }}">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-block rounded-full bg-navy-50 px-2.5 py-0.5 text-xs font-bold text-navy-700 dark:bg-navy-500/10 dark:text-navy-400">
                                            {{ $leader->period_label }}
                                        </span>
                                        @if ($loop->last)
                                            <span
                                                class="inline-block rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                                Periode Terkini
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-base font-bold text-slate-800 dark:text-white">
                                        {{ $leader->name }}</p>
                                    @if ($leader->major)
                                        <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $leader->major }}</p>
                                    @else
                                        <p class="mt-0.5 text-sm italic text-slate-400 dark:text-slate-600">
                                            Jurusan/Kampus belum tersedia</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ NAVIGASI CEPAT ============ --}}
        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-3">

            {{-- Navigasi 1: Visi & Misi (Biru) --}}
            <a href="{{ route('public.about.vision') }}"
                class="group rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-blue-200 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-900/50">
                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600 transition-colors duration-300 group-hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:group-hover:bg-blue-500/20">
                    {{-- Ikon Buku Terbuka (Visi Misi) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h3
                    class="text-base font-semibold text-slate-800 transition-colors group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">
                    Visi & Misi</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Landasan dan arah gerak organisasi.</p>
            </a>

            {{-- Navigasi 2: Struktur Pengurus (Gold) --}}
            <a href="{{ route('public.about.structure') }}"
                class="group rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-amber-200 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-amber-900/50">
                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-amber-50 text-amber-600 transition-colors duration-300 group-hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:group-hover:bg-amber-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <h3
                    class="text-base font-semibold text-slate-800 transition-colors group-hover:text-amber-600 dark:text-white dark:group-hover:text-amber-400">
                    Struktur Pengurus</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Kenali para penggerak KATIBER.</p>
            </a>

            {{-- Navigasi 3: Agenda Kegiatan (Grey) --}}
            <a href="{{ route('public.agenda.index') }}"
                class="group rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700/50">
                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition-colors duration-300 group-hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:group-hover:bg-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <h3
                    class="text-base font-semibold text-slate-800 transition-colors group-hover:text-slate-700 dark:text-white dark:group-hover:text-slate-300">
                    Agenda Kegiatan</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Jangan lewatkan momen seru kami.</p>
            </a>

        </div>
    </section>
@endsection
