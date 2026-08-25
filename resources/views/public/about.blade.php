@extends('layouts.public')

@section('title', 'Tentang Kami - KATIBER')

@section('content')
    <section class="border-b border-slate-100 bg-gradient-to-b from-blue-50 to-white dark:border-slate-800 dark:from-slate-900 dark:to-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white sm:text-4xl">Tentang Kami</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-slate-500 dark:text-slate-400">
                KATIBER — Keluarga Mahasiswa Tebing Tinggi Bersatu — adalah organisasi kekeluargaan yang menaungi mahasiswa asal Tebing Tinggi yang menempuh pendidikan di Lhokseumawe, Aceh Utara. Kami hadir sebagai wadah silaturahmi, saling membantu, dan mengembangkan potensi diri di tanah rantau.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-10">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Sejarah Singkat</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. KATIBER dibentuk atas inisiatif mahasiswa asal Tebing Tinggi yang menempuh pendidikan di Lhokseumawe untuk mempererat tali silaturahmi dan menjadi wadah saling membantu antar anggota. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua, seiring waktu organisasi ini berkembang menjadi rumah kedua bagi mahasiswa perantauan.
            </p>
            <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Hingga saat ini, KATIBER terus konsisten menjalankan berbagai program kaderisasi, pembinaan akademik, kegiatan sosial, dan pengembangan minat bakat bagi seluruh anggotanya.
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <a href="{{ route('public.about.vision') }}"
                class="theme-transition group rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-800 transition group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">Visi & Misi</p>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Landasan dan arah gerak organisasi.</p>
            </a>
            <a href="{{ route('public.about.structure') }}"
                class="theme-transition group rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-800 transition group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">Struktur Pengurus</p>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Kenali para penggerak KATIBER.</p>
            </a>
            <a href="{{ route('public.agenda.index') }}"
                class="theme-transition group rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-800 transition group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">Agenda Kegiatan</p>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Jangan lewatkan momen seru kami.</p>
            </a>
        </div>
    </section>
@endsection
