@extends('layouts.app')

@section('title', 'Audit Log - KATIBER')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Riwayat Aktivitas (Audit Log)</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Catatan sistem untuk memantau perubahan data.</p>
    </div>

    <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                        <th class="w-48 px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Waktu</th>
                        <th class="w-48 px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">User</th>
                        <th class="w-48 px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Detail Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($logs as $log)
                        <tr class="text-sm transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800 dark:text-white">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ $log->action }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-300 dark:text-slate-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l1.5 1.5 3-3.75m-4.5-6.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286z" />
                                    </svg>
                                    <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada riwayat aktivitas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="border-t border-slate-100 px-5 py-4 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
