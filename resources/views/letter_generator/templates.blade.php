@extends('layouts.app')

@section('title', $heading . ' - KATIBER')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ $backUrl }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">{{ $heading }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    @if ($committee)
                        Pilih jenis surat. Nama acara, tema, tanggal, tempat, dan penandatangan terisi otomatis dari data kepanitiaan.
                    @else
                        Pilih jenis surat. Nomor surat dan penandatangan terisi otomatis.
                    @endif
                </p>
            </div>
        </div>

        @if ($committee && (! $committee->theme || ! $committee->venue || empty($committee->event_dates) || ! $committee->start_time))
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                Data acara belum lengkap (tema, tanggal acara, jam, atau tempat masih kosong), jadi sebagian isian surat harus diketik manual.
                @can('manage_committees')
                    <a href="{{ route('committees.edit', $committee->id) }}" class="font-semibold underline">Lengkapi di data kepanitiaan</a>
                    supaya semua surat berikutnya langsung terisi.
                @endcan
            </div>
        @endif

        @if ($templates->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                Belum ada template. Jalankan <code>php artisan db:seed --class=LetterTemplateSeeder</code>.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($templates as $t)
                    <a href="{{ $nextRoute($t) }}"
                        class="theme-transition group flex flex-col rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-500/50">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <h2 class="font-semibold text-slate-800 group-hover:text-blue-700 dark:text-white dark:group-hover:text-blue-400">{{ $t->name }}</h2>
                            <span title="{{ $t->letter_type === 'A' ? 'Surat Internal' : 'Surat Eksternal' }}"
                                class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $t->letter_type === 'A' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' }}">
                                {{ $t->letter_type === 'A' ? 'Internal (A)' : 'Eksternal (B)' }}
                            </span>
                        </div>
                        <p class="flex-1 text-sm text-slate-500 dark:text-slate-400">{{ $t->description }}</p>
                        <p class="mt-3 text-xs font-semibold text-blue-600 dark:text-blue-400">Pakai template ini &rarr;</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
