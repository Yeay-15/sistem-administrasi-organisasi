@php
    $c = $committee;
@endphp
<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Kepanitiaan</label>
            <input type="text" name="name" value="{{ old('name', $c->name ?? '') }}" required
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            @error('name')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jenis (opsional)</label>
            <input type="text" name="type" value="{{ old('type', $c->type ?? '') }}" placeholder="mis. Internal, Kerjasama, Kompetisi"
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            @error('type')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
            <select name="status" required class="block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @foreach (['Persiapan', 'Berjalan', 'Selesai'] as $status)
                    <option value="{{ $status }}" {{ old('status', $c->status ?? 'Persiapan') === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Mulai (opsional)</label>
            <input type="date" name="start_date" value="{{ old('start_date', isset($c->start_date) ? $c->start_date?->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:[color-scheme:dark]">
            @error('start_date')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Selesai (opsional)</label>
            <input type="date" name="end_date" value="{{ old('end_date', isset($c->end_date) ? $c->end_date?->format('Y-m-d') : '') }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:[color-scheme:dark]">
            @error('end_date')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi (opsional)</label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">{{ old('description', $c->description ?? '') }}</textarea>
            @error('description')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ $c ? route('kepanitiaan.show', $c->id) : route('kepanitiaan.index') }}"
            class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
        <button type="submit"
            class="rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md">Simpan</button>
    </div>
</form>
