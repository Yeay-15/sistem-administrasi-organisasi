@extends('layouts.app')

@section('title', 'Data Inventaris - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Data Inventaris</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Daftar barang organisasi beserta jumlah, tempat penyimpanan, dan masa kedaluwarsanya.</p>
        </div>
        @can('manage_inventory_items')
        <div class="flex flex-col gap-2 sm:flex-row">
        <a href="{{ route('inventory-items.import') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            Impor Excel
        </a>
        <a href="{{ route('inventory-items.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Barang
        </a>
        </div>
        @endcan
    </div>

    <!-- Ringkasan -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jenis Barang</p>
            <p class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tempat Penyimpanan</p>
            <p class="mt-1 text-2xl font-bold text-slate-800 dark:text-white">{{ $stats['locations'] }}</p>
        </div>
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Perlu Perhatian (Kedaluwarsa &le; {{ \App\Models\InventoryItem::EXPIRY_WARNING_DAYS }} hari)</p>
            <p class="mt-1 text-2xl font-bold {{ $stats['attention'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-white' }}">{{ $stats['attention'] }}</p>
        </div>
    </div>

    <!-- Form Filter -->
    <form action="{{ route('inventory-items.index') }}" method="GET"
        class="theme-transition mb-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Urutan</label>
                <x-dropdown-select name="sort" :selected="$sort"
                    on-select="$nextTick(() => $root.closest('form').submit())"
                    :options="['name' => 'Nama (A-Z)', 'location' => 'Tempat', 'expiry' => 'Kedaluwarsa Terdekat', 'newest' => 'Terbaru Ditambahkan']" />
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tempat</label>
                <x-dropdown-select name="location" placeholder="Semua Tempat" :selected="request('location')"
                    :options="$locationOptions" />
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Masa Pakai</label>
                <x-dropdown-select name="status" placeholder="Semua" :selected="request('status')"
                    :options="['expired' => 'Sudah Kedaluwarsa', 'soon' => 'Segera Kedaluwarsa']" />
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pencarian</label>
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Tempat / Keterangan..."
                        class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <button type="submit"
                    class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600">
                    Terapkan Filter
                </button>
                <a href="{{ route('inventory-items.index') }}"
                    class="text-sm font-medium text-slate-500 transition hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">Reset</a>
            </div>
            <div class="flex gap-2">
                <button type="submit" name="export" value="excel"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </button>
                <button type="submit" name="export" value="pdf"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
    </form>

    <!-- Tabel Data -->
    <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">No</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Barang</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jumlah</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Satuan</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tempat</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Ditambahkan</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kedaluwarsa</th>
                        @canany(['manage_inventory_items', 'delete_inventory_items'])
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($items as $item)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-5 py-4 text-sm text-slate-400 dark:text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white" @if ($item->notes) title="{{ $item->notes }}" @endif>{{ $item->name }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">{{ $item->quantity_label }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                @if ($item->unit)
                                    {{ $item->unit }}
                                @else
                                    <span class="italic text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                @if ($item->location)
                                    {{ $item->location }}
                                @else
                                    <span class="italic text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ $item->added_at ? $item->added_at->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($item->expired_at)
                                    @php $expiry = $item->expiry_status; @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-slate-600 dark:text-slate-300">{{ $item->expired_at->translatedFormat('d M Y') }}</span>
                                        @if ($expiry === 'expired')
                                            <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-600 dark:bg-red-500/10 dark:text-red-400">Kedaluwarsa</span>
                                        @elseif ($expiry === 'soon')
                                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">Segera</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm italic text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                            @canany(['manage_inventory_items', 'delete_inventory_items'])
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @can('manage_inventory_items')
                                        <a href="{{ route('inventory-items.edit', $item->id) }}" title="Edit"
                                            class="rounded-lg p-2 text-amber-600 transition hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-500/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-3.75-3.75" />
                                            </svg>
                                        </a>
                                        @endcan
                                        @can('delete_inventory_items')
                                        <form action="{{ route('inventory-items.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus barang ini dari inventaris secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10 text-slate-300 dark:text-slate-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                    <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada data inventaris yang cocok.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->isNotEmpty())
            <div class="border-t border-slate-100 px-5 py-3 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
                Menampilkan {{ $items->count() }} jenis barang.
            </div>
        @endif
    </div>
@endsection
