@extends('layouts.app')

@section('title', 'Buat Kepanitiaan - KATIBER')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('kepanitiaan.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Buat Kepanitiaan Baru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Setelah dibuat, Anda bisa menambahkan anggota panitia & mengaitkan agenda.</p>
            </div>
        </div>

        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            @include('committees._form', ['action' => route('kepanitiaan.store'), 'method' => 'POST', 'committee' => null])
        </div>
    </div>
@endsection
