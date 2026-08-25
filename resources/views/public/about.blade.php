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

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Susunan Kepengurusan</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pengurus aktif yang menjalankan roda organisasi.</p>
        </div>

        @forelse ($divisions as $division)
            @if ($division->members->isNotEmpty())
                <div class="mb-12">
                    <div class="mb-5 flex items-center gap-3">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $division->name }}</h3>
                        <span class="h-px flex-1 bg-slate-100 dark:bg-slate-800"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($division->members as $member)
                            <div class="theme-transition group rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                                <div class="mx-auto mb-3 h-20 w-20 overflow-hidden rounded-full bg-slate-100 ring-2 ring-slate-100 transition group-hover:ring-blue-200 dark:bg-slate-800 dark:ring-slate-800 dark:group-hover:ring-blue-500/30">
                                    @if ($member->photo_url)
                                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xl font-bold text-slate-400 dark:text-slate-600">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">{{ $member->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-blue-600 dark:text-blue-400">{{ $member->position }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <p class="text-center text-sm text-slate-400 dark:text-slate-500">Data pengurus belum tersedia.</p>
        @endforelse
    </section>
@endsection
