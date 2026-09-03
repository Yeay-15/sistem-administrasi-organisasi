@extends('layouts.app')

@section('title', 'Aspirasi Mahasiswa - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Aspirasi Mahasiswa</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pesan yang dikirim mahasiswa melalui formulir Kontak & Aspirasi di Portal Publik.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if ($unreadCount > 0)
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    {{ $unreadCount }} belum dibaca
                </span>
            @endif
            @if ($aspirations->isNotEmpty())
                {{-- Export mengikuti filter kategori/unread_only yang sedang aktif --}}
                <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </a>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </a>
            @endif
        </div>
    </div>

    <form method="GET" class="mb-5 flex flex-wrap gap-2">
        <a href="{{ route('aspirations.index') }}"
            class="rounded-lg border px-3.5 py-2 text-sm font-medium transition {{ ! request('category') ? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-400' : 'border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800' }}">
            Semua
        </a>
        @foreach (['Aspirasi', 'Kritik Saran', 'Laporan', 'Pertanyaan'] as $cat)
            <a href="{{ route('aspirations.index', ['category' => $cat]) }}"
                class="rounded-lg border px-3.5 py-2 text-sm font-medium transition {{ request('category') === $cat ? 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-400' : 'border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                {{ $cat }}
            </a>
        @endforeach
    </form>

    @if ($aspirations->isEmpty())
        <div class="theme-transition rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada pesan aspirasi yang masuk.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($aspirations as $aspiration)
                <div class="theme-transition rounded-2xl border bg-white p-5 shadow-sm dark:bg-slate-900
                    {{ $aspiration->is_read ? 'border-slate-100 dark:border-slate-800' : 'border-blue-200 dark:border-blue-500/30' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $aspiration->display_name }}</p>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500 dark:bg-slate-800 dark:text-slate-400">{{ $aspiration->category }}</span>
                                @if (! $aspiration->is_read)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">Baru</span>
                                @endif
                            </div>
                            @if ($aspiration->contact)
                                <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Kontak: {{ $aspiration->contact }}</p>
                            @endif
                            <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $aspiration->message }}</p>
                            <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">{{ $aspiration->created_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>

                        <div class="flex shrink-0 items-center gap-1.5">
                            @if (! $aspiration->is_read)
                                <form action="{{ route('aspirations.mark-as-read', $aspiration->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Tandai sudah dibaca"
                                        class="rounded-lg p-2 text-emerald-600 transition hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-500/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            @can('delete_aspirations')
                            <form action="{{ route('aspirations.destroy', $aspiration->id) }}" method="POST"
                                onsubmit="return confirm('Hapus pesan aspirasi ini?');">
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
            {{ $aspirations->links() }}
        </div>
    @endif
@endsection
