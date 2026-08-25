@extends('layouts.public')

@section('title', 'Hubungi Kami - KATIBER')

@section('content')
    <section class="border-b border-slate-100 bg-slate-50 dark:border-slate-800 dark:bg-slate-900/40">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Hubungi Kami</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Ada pertanyaan atau ingin bersilaturahmi? Hubungi kami melalui kanal berikut.</p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="theme-transition flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Alamat Sekretariat</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Lhokseumawe, Aceh Utara, Indonesia</p>
                </div>
            </div>

            <div class="theme-transition flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">Kontak Pengurus</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Hubungi Sekretaris Umum atau Kadiv Infokom melalui media sosial di bawah.</p>
                </div>
            </div>
        </div>

        <div class="theme-transition mt-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="mb-4 text-sm font-semibold text-slate-800 dark:text-white">Media Sosial</p>
            <div class="flex flex-wrap gap-3">
                <a href="#" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Instagram
                </a>
                <a href="#" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    WhatsApp
                </a>
                <a href="#" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    TikTok
                </a>
            </div>
            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">Tautan media sosial di atas masih placeholder — mohon diganti dengan tautan resmi KATIBER.</p>
        </div>
    </section>
@endsection
