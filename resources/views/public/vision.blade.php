@extends('layouts.public')

@section('title', 'Visi & Misi - KATIBER')

@section('content')
    <section class="border-b border-slate-100 bg-gradient-to-b from-blue-50 to-white dark:border-slate-800 dark:from-slate-900 dark:to-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white sm:text-4xl">Visi & Misi</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-slate-500 dark:text-slate-400">
                Landasan dan arah gerak KATIBER dalam menjalankan setiap program kerja.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-10">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Visi</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Menjadi organisasi kekeluargaan mahasiswa Tebing Tinggi yang solid, berprestasi, dan berkontribusi positif bagi almamater serta masyarakat.
            </p>
        </div>

        <div class="theme-transition mt-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-10">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Misi</h2>
            <ul class="mt-3 space-y-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                <li class="flex gap-3">
                    <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">1</span>
                    <span>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua — mempererat silaturahmi antar mahasiswa Tebing Tinggi di Lhokseumawe.</span>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">2</span>
                    <span>Ut enim ad minim veniam, quis nostrud exercitation — mendorong prestasi akademik dan non-akademik anggota.</span>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">3</span>
                    <span>Duis aute irure dolor in reprehenderit — menjadi wadah pengembangan minat, bakat, dan kepemimpinan mahasiswa.</span>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">4</span>
                    <span>Excepteur sint occaecat cupidatat non proident — berperan aktif dalam kegiatan sosial dan kemasyarakatan.</span>
                </li>
            </ul>
        </div>
    </section>
@endsection
