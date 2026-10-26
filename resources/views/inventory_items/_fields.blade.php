{{--
    Kolom isian bersama untuk halaman Tambah & Ubah barang inventaris.
    Variabel: $item (InventoryItem|null), $units.
--}}
@php
    $inputClass = 'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]';
    $labelClass = 'mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300';
    $errorClass = 'mt-1.5 text-sm text-red-500 dark:text-red-400';

    $currentUnit = (string) old('unit', $item?->unit ?? '');
    $addedAt = old('added_at', ($item?->added_at ?? today())->format('Y-m-d'));

    // Jumlah dirender sebagai string rapi ("7", "0.5") supaya aman di input number.
    $quantityValue = old('quantity', $item ? rtrim(rtrim(number_format((float) $item->quantity, 2, '.', ''), '0'), '.') : '1');
@endphp

<div>
    <label class="{{ $labelClass }}">Nama Barang</label>
    <input type="text" name="name" value="{{ old('name', $item?->name) }}" placeholder="Contoh: Handy Talky (HT)" required
        class="{{ $inputClass }}">
    @error('name')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>

<div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="{{ $labelClass }}">Jumlah</label>
        <input type="number" name="quantity" value="{{ $quantityValue }}" min="0" step="any" required autocomplete="off"
            class="{{ $inputClass }}">
        @error('quantity')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="{{ $labelClass }}">Satuan (Opsional)</label>
        <x-select-with-custom name="unit" :options="$units" :selected="$currentUnit" :maxlength="50"
            empty-label="Tanpa satuan" custom-label="+ Satuan lain..." custom-placeholder="Ketik satuan baru, mis. lembar" />
        @error('unit')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="{{ $labelClass }}">Tempat (Opsional)</label>
        <input type="text" name="location" value="{{ old('location', $item?->location) }}" maxlength="100" placeholder="Contoh: Sekret 24"
            class="{{ $inputClass }}">
        @error('location')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="{{ $labelClass }}">Tanggal Ditambahkan</label>
        <input type="date" name="added_at" value="{{ $addedAt }}" required
            class="{{ $inputClass }}">
        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Otomatis hari ini untuk barang baru, bisa diubah.</p>
        @error('added_at')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label class="{{ $labelClass }}">Tanggal Kedaluwarsa (Opsional)</label>
        <input type="date" name="expired_at" value="{{ old('expired_at', $item?->expired_at?->format('Y-m-d')) }}"
            class="{{ $inputClass }}">
        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Isi untuk obat/barang yang punya masa pakai.</p>
        @error('expired_at')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-5">
    <label class="{{ $labelClass }}">Keterangan (Opsional)</label>
    <textarea name="notes" rows="3"
        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('notes', $item?->notes) }}</textarea>
    @error('notes')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>
