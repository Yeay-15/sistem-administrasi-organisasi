@extends('layouts.app')

@section('title', 'Kelola Prestasi - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Prestasi Mahasiswa</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola daftar prestasi yang tampil di bagian "Prestasi Mahasiswa" pada Beranda Portal Publik.</p>
        </div>
        @can('manage_achievements')
        <a href="{{ route('achievements.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Prestasi
        </a>
        @endcan
    </div>

    @if ($achievements->isEmpty())
        <div class="theme-transition rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada data prestasi. Tambahkan prestasi pertama untuk ditampilkan di Beranda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($achievements as $achievement)
                <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="aspect-square w-full overflow-hidden bg-slate-100 dark:bg-slate-800">
                        @if ($achievement->photo_url)
                            <img src="{{ $achievement->photo_url }}" alt="{{ $achievement->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-slate-300 dark:text-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <p class="line-clamp-2 text-sm font-bold text-slate-800 dark:text-white">{{ $achievement->title }}</p>
                        <p class="mt-1 truncate text-xs font-medium text-blue-600 dark:text-blue-400">{{ $achievement->achiever_name }}</p>
                        @if ($achievement->description)
                            <p class="mt-1 truncate text-xs text-slate-400 dark:text-slate-500">{{ $achievement->description }}</p>
                        @endif

                        <div class="mt-4 flex items-center justify-end gap-1.5 border-t border-slate-100 pt-3 dark:border-slate-800">
                            @can('manage_achievements')
                            <a href="{{ route('achievements.edit', $achievement->id) }}" title="Edit"
                                class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                </svg>
                            </a>
                            @endcan
                            @can('delete_achievements')
                            <form action="{{ route('achievements.destroy', $achievement->id) }}" method="POST"
                                onsubmit="return confirm('Hapus prestasi ini?');">
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
            {{ $achievements->links() }}
        </div>
    @endif
@endsection
