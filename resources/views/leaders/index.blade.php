@extends('layouts.app')

@section('title', 'Estafet Kepemimpinan - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Estafet Kepemimpinan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola daftar Ketua Umum yang tampil di timeline "Estafet Kepemimpinan" pada halaman Tentang Kami.</p>
        </div>
        @can('manage_leaders')
        <a href="{{ route('leaders.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Ketua Umum
        </a>
        @endcan
    </div>

    @if ($leaders->isEmpty())
        <div class="theme-transition rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada data Ketua Umum. Tambahkan data pertama untuk ditampilkan di halaman Tentang Kami.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($leaders as $leader)
                <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col items-center p-5 text-center">
                        <div class="h-20 w-20 overflow-hidden rounded-full bg-slate-100 ring-2 ring-slate-100 dark:bg-slate-800 dark:ring-slate-800">
                            @if ($leader->photo_url)
                                <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-9 w-9">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <span class="mt-3 inline-block rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                            {{ $leader->period_label }}
                        </span>
                        <p class="mt-2 line-clamp-2 text-sm font-bold text-slate-800 dark:text-white">{{ $leader->name }}</p>
                        @if ($leader->major)
                            <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">{{ $leader->major }}</p>
                        @else
                            <p class="mt-0.5 text-xs italic text-slate-400 dark:text-slate-600">Jurusan/Kampus belum diisi</p>
                        @endif

                        <div class="mt-4 flex w-full items-center justify-center gap-1.5 border-t border-slate-100 pt-3 dark:border-slate-800">
                            @can('manage_leaders')
                            <a href="{{ route('leaders.edit', $leader->id) }}" title="Edit"
                                class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                </svg>
                            </a>
                            @endcan
                            @can('delete_leaders')
                            <form action="{{ route('leaders.destroy', $leader->id) }}" method="POST"
                                onsubmit="return confirm('Hapus data Ketua Umum ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus"
                                    class="rounded-lg p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $leaders->links() }}
        </div>
    @endif
@endsection
