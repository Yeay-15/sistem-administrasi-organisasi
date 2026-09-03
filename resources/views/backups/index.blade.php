@extends('layouts.app')

@section('title', 'Cadangan Data - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Cadangan Data</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Database + seluruh foto/dokumen dibungkus jadi satu file .zip otomatis setiap hari jam 02:00,
                disimpan 7 hari terakhir (harian) dan 4 minggu terakhir (mingguan). Halaman ini khusus Super Admin.
            </p>
        </div>
        <form action="{{ route('backups.store') }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205L12 12m6.894 5.785l-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864l-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                </svg>
                Buat Cadangan Sekarang
            </button>
        </form>
    </div>

    {{-- Status tujuan cadangan — supaya jelas apakah off-site (Google Drive)
         sudah aktif atau belum, tanpa perlu buka .env. --}}
    <div class="mb-5 flex flex-wrap gap-2">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            Laptop Server (local) — aktif
        </span>
        @if ($googleDriveActive)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                Google Drive (off-site) — aktif
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                Google Drive (off-site) — belum dikonfigurasi
            </span>
        @endif
    </div>

    @if (session('success'))
        <div class="theme-transition mb-5 flex items-center justify-between gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800/60 dark:bg-green-500/10 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="theme-transition mb-5 flex items-center justify-between gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800/60 dark:bg-red-500/10 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    @if ($backups->isEmpty())
        <div class="theme-transition rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center dark:border-slate-800 dark:bg-slate-900">
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada cadangan. Klik "Buat Cadangan Sekarang" untuk membuat yang pertama.</p>
        </div>
    @else
        <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nama File</th>
                        <th class="px-5 py-3 font-semibold">Ukuran</th>
                        <th class="px-5 py-3 font-semibold">Dibuat</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($backups as $backup)
                        <tr>
                            <td class="px-5 py-4 font-mono text-xs text-slate-700 dark:text-slate-300">{{ $backup['name'] }}</td>
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ number_format($backup['size'] / 1048576, 1) }} MB</td>
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ $backup['modified_at']->translatedFormat('d M Y, H:i') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('backups.download', $backup['name']) }}"
                                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                        Unduh
                                    </a>
                                    <form action="{{ route('backups.destroy', $backup['name']) }}" method="POST"
                                        onsubmit="return confirm('Hapus cadangan \'{{ $backup['name'] }}\'? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg border border-red-200 p-1.5 text-red-500 transition hover:bg-red-50 dark:border-red-800/60 dark:text-red-400 dark:hover:bg-red-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
