@php
    $event = $event ?? null;
@endphp

<div>
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Event</label>
    <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" required
        placeholder="Contoh: KATIBER Cup 2026"
        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
    @error('title')
        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Mulai</label>
        <input type="date" name="event_start_date" value="{{ old('event_start_date', isset($event) ? $event->event_start_date->format('Y-m-d') : '') }}" required
            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        @error('event_start_date')
            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Selesai (Opsional)</label>
        <input type="date" name="event_end_date" value="{{ old('event_end_date', isset($event) && $event->event_end_date ? $event->event_end_date->format('Y-m-d') : '') }}"
            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        @error('event_end_date')
            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-5">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Lokasi (Opsional)</label>
    <input type="text" name="location" value="{{ old('location', $event->location ?? '') }}" placeholder="Contoh: GOR Serbaguna Tebing Tinggi"
        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
    @error('location')
        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<div class="mt-5">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Ringkasan Singkat (Opsional)</label>
    <textarea name="short_description" rows="2" maxlength="500" placeholder="Ringkasan singkat yang tampil di hero banner beranda &amp; pita pengumuman."
        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('short_description', $event->short_description ?? '') }}</textarea>
    @error('short_description')
        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<div class="mt-5">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Poster / Banner Event</label>
    <div class="flex items-center gap-4">
        <div class="flex h-20 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
            <template x-if="posterPreview">
                <img :src="posterPreview" alt="Preview poster" class="h-full w-full object-cover">
            </template>
            <template x-if="!posterPreview">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300 dark:text-slate-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                </svg>
            </template>
        </div>
        <div class="flex-1">
            <input type="file" name="poster" accept="image/png, image/jpeg, image/webp"
                @change="posterPreview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : posterPreview"
                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
            <p class="mt-1 text-xs text-slate-400">Dipakai di hero banner beranda, pita pengumuman, dan halaman detail. Maks. 3MB.</p>
            @if (isset($event) && $event->poster_path)
                <label class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500">
                    <input type="checkbox" name="remove_poster" value="1" class="rounded border-slate-300"> Hapus poster saat ini
                </label>
            @endif
            @error('poster')
                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="mt-5">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Isi Halaman Detail (Opsional)</label>
    <div id="editor-container" style="min-height: 200px;" class="theme-transition rounded-lg border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-800"></div>
    <input type="hidden" name="content" x-ref="contentInput">
    <p class="mt-1 text-xs text-slate-400">Syarat &amp; ketentuan pendaftaran, total hadiah, aturan pertandingan, dsb. Tampil di halaman /event/{{ $event->slug ?? '...' }}.</p>
    @error('content')
        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Link Pendaftaran (Opsional)</label>
        <input type="url" name="registration_url" value="{{ old('registration_url', $event->registration_url ?? '') }}" placeholder="https://forms.gle/..."
            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
        <p class="mt-1 text-xs text-slate-400">Google Form, WhatsApp panitia, atau tautan lain.</p>
        @error('registration_url')
            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Teks Tombol</label>
        <input type="text" name="cta_label" value="{{ old('cta_label', $event->cta_label ?? 'Daftar Sekarang') }}" maxlength="50"
            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        @error('cta_label')
            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6 rounded-xl border border-slate-200 p-4 dark:border-slate-700">
    <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300">
        <input type="checkbox" name="has_bracket" value="1"
            {{ old('has_bracket', $event->has_bracket ?? false) ? 'checked' : '' }}
            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
        Event ini berbentuk turnamen (mis. futsal) — pakai fase grup, klasemen &amp; babak gugur
    </label>
    <p class="mt-1.5 text-xs text-slate-400">Jumlah tim, pembagian grup, dan pasangan pertandingan diatur belakangan lewat menu "Kelola Bagan" di daftar event — tidak perlu ditentukan di sini, dan bisa menyesuaikan berapa pun tim yang mendaftar.</p>
</div>


<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
        <select name="status"
            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
            @foreach (\App\Models\FeaturedEvent::STATUSES as $value => $label)
                <option value="{{ $value }}" {{ old('status', $event->status ?? 'draft') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-400">Hanya event berstatus Aktif yang bisa muncul di beranda/pengumuman. Arsip tetap bisa dibuka publik sebagai rekam jejak.</p>
    </div>
    <div class="flex flex-col justify-center gap-2.5">
        <label class="flex items-center gap-2.5 text-sm text-slate-700 dark:text-slate-300">
            <input type="checkbox" name="show_on_homepage" value="1" {{ old('show_on_homepage', $event->show_on_homepage ?? false) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            Tampilkan sebagai hero banner beranda
        </label>
        <label class="flex items-center gap-2.5 text-sm text-slate-700 dark:text-slate-300">
            <input type="checkbox" name="show_announcement_bar" value="1" {{ old('show_announcement_bar', $event->show_announcement_bar ?? false) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            Tampilkan di pita pengumuman (semua halaman)
        </label>
    </div>
</div>
