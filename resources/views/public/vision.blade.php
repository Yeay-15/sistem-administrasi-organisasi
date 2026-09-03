@extends('layouts.public')

@section('title', 'Visi & Misi - KATIBER')

@section('content')
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950">
        <div class="navy-dot-pattern absolute inset-0"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-20 text-center sm:px-6 lg:px-8">
            <span
                class="reveal inline-block rounded-full bg-white/10 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-white ring-1 ring-inset ring-white/15">Landasan
                Gerak</span>
            <h1 class="reveal mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-5xl" style="--reveal-delay:80ms">
                Visi & Misi</h1>
            <p class="reveal mx-auto mt-4 max-w-2xl text-base leading-relaxed text-navy-100 sm:text-lg"
                style="--reveal-delay:160ms">
                Landasan dan arah gerak KATIBER dalam menjalankan setiap program kerja.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
        <div
            class="reveal theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 bg-gradient-to-r from-navy-700 to-navy-900 px-6 py-5 sm:px-10">
                <h2 class="text-lg font-bold text-white">Visi</h2>
            </div>
            <p class="px-6 py-6 text-sm leading-relaxed text-slate-600 dark:text-slate-300 sm:px-10">
                Mewujudkan insan akademis yang mandiri, kreatif, dan profesional, serta membangun generasi mahasiswa asal
                Kota Tebing Tinggi yang memiliki ikatan persaudaraan kokoh, berwawasan kebangsaan, dan bertanggung jawab
                sebagai pemimpin yang menjunjung tinggi kebenaran, keadilan, dan kedisiplinan.
            </p>
        </div>

        <div class="reveal theme-transition mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            style="--reveal-delay:120ms">
            <div class="border-b border-slate-100 bg-gradient-to-r from-navy-700 to-navy-900 px-6 py-5 sm:px-10">
                <h2 class="text-lg font-bold text-white">Misi</h2>
            </div>
            <ul class="space-y-4 px-6 py-6 text-sm leading-relaxed text-slate-600 dark:text-slate-300 sm:px-10">
                <li class="flex gap-3">
                    <span
                        class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy-50 text-xs font-bold text-navy-700 dark:bg-navy-500/10 dark:text-navy-400">1</span>
                    <span>Mempererat tali persaudaraan antar sesama mahasiswa asal Kota Tebing Tinggi
                        dan sekitarnya yang berada di Lhokseumawe dan Aceh Utara.</span>
                </li>
                <li class="flex gap-3">
                    <span
                        class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy-50 text-xs font-bold text-navy-700 dark:bg-navy-500/10 dark:text-navy-400">2</span>
                    <span>Membangun wadah komunikasi dan kerja sama yang efektif dalam menampung, menyalurkan, dan
                        melaksanakan aspirasi anggota.</span>
                </li>
                <li class="flex gap-3">
                    <span
                        class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy-50 text-xs font-bold text-navy-700 dark:bg-navy-500/10 dark:text-navy-400">3</span>
                    <span>Mempersiapkan kader pemimpin yang tanggap dan berintelektual dengan berlandaskan pada Tri Dharma
                        Perguruan Tinggi (Pendidikan, Penelitian, dan Pengabdian).</span>
                </li>
                <li class="flex gap-3">
                    <span
                        class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-navy-50 text-xs font-bold text-navy-700 dark:bg-navy-500/10 dark:text-navy-400">4</span>
                    <span>Melaksanakan kegiatan-kegiatan kemahasiswaan yang bersifat demokratis, kekeluargaan, dan mandiri
                        tanpa berafiliasi pada kepentingan politik praktis.</span>
                </li>
            </ul>
        </div>
    </section>
@endsection
