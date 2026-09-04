@extends('layouts.public')

@section('title', 'Event Unggulan - KATIBER')
@section('meta_description', 'Acara dan turnamen besar yang diselenggarakan KATIBER, termasuk KATIBER Cup — turnamen futsal antar SMA se-Provinsi Sumatera Utara.')

@section('content')
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950">
        <div class="navy-dot-pattern absolute inset-0"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <h1 class="reveal text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Event Unggulan</h1>
            <p class="reveal mt-2 max-w-2xl text-sm text-navy-100" style="--reveal-delay:80ms">Acara-acara besar yang kami selenggarakan, termasuk KATIBER Cup — turnamen futsal antar SMA se-Provinsi Sumatera Utara.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
        @if ($activeEvents->isNotEmpty())
            <h2 class="mb-5 text-lg font-bold text-slate-800 dark:text-white">Sedang Berlangsung</h2>
            <div class="mb-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($activeEvents as $event)
                    @include('public.events._card', ['event' => $event, 'loopIndex' => $loop->index])
                @endforeach
            </div>
        @endif

        <h2 class="mb-5 text-lg font-bold text-slate-800 dark:text-white">Rekam Jejak Acara Sebelumnya</h2>
        @if ($archivedEvents->isEmpty())
            <p class="text-center text-sm text-slate-400 dark:text-slate-500">Belum ada arsip acara.</p>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($archivedEvents as $event)
                    @include('public.events._card', ['event' => $event, 'loopIndex' => $loop->index])
                @endforeach
            </div>
            <div class="mt-8">{{ $archivedEvents->links() }}</div>
        @endif
    </section>
@endsection
