@extends('layouts.app')

@section('title', 'Pengaturan Beranda - KATIBER')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pengaturan Beranda</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola konten dinamis pada bagian Hero dan Sambutan Ketua Umum di halaman Beranda Portal Publik.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ============ HERO ============ --}}
            <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8"
                x-data="{ preview: @js($setting->hero_image_url), removed: false }">
                <h2 class="text-base font-bold text-slate-800 dark:text-white">Bagian Hero</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tampil sebagai gambar latar dan judul besar di paling atas Beranda.</p>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Gambar Latar (Hero)</label>
                    <div class="flex items-center gap-4">
                        <div class="flex h-20 w-32 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <template x-if="preview && !removed">
                                <img :src="preview" alt="Preview hero" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!preview || removed">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300 dark:text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25M3 8.25l9-6 9 6" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="hero_image" accept="image/png, image/jpeg, image/webp"
                                @change="if ($event.target.files.length) { preview = URL.createObjectURL($event.target.files[0]); removed = false }"
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                            <p class="mt-1 text-xs text-slate-400">Disarankan gambar lanskap. Maks. 4MB.</p>
                            @error('hero_image')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            @if ($setting->hero_image_path)
                                <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                    <input type="checkbox" name="remove_hero_image" value="1" x-model="removed"
                                        class="rounded border-slate-300 text-red-600 focus:ring-red-500 dark:border-slate-600 dark:bg-slate-800">
                                    Hapus gambar saat ini
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Ucapan Selamat Datang</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}"
                        placeholder="Contoh: Keluarga Mahasiswa Tebing Tinggi Bersatu"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    @error('hero_title')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Subjudul/Deskripsi Singkat</label>
                    <textarea name="hero_subtitle" rows="3"
                        placeholder="Kalimat singkat penjelas di bawah judul Hero."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                    @error('hero_subtitle')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ============ SAMBUTAN KETUA UMUM ============ --}}
            <div class="theme-transition mt-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8"
                x-data="{ preview: @js($setting->chairman_photo_url), removed: false }">
                <h2 class="text-base font-bold text-slate-800 dark:text-white">Sambutan Ketua Umum</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Foto dan pesan sambutan yang tampil di Beranda.</p>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Foto Ketua Umum</label>
                    <div class="flex items-center gap-4">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <template x-if="preview && !removed">
                                <img :src="preview" alt="Preview foto ketua umum" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!preview || removed">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300 dark:text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="chairman_photo" accept="image/png, image/jpeg, image/webp"
                                @change="if ($event.target.files.length) { preview = URL.createObjectURL($event.target.files[0]); removed = false }"
                                class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                            <p class="mt-1 text-xs text-slate-400">Maks. 2MB.</p>
                            @error('chairman_photo')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            @if ($setting->chairman_photo_path)
                                <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                    <input type="checkbox" name="remove_chairman_photo" value="1" x-model="removed"
                                        class="rounded border-slate-300 text-red-600 focus:ring-red-500 dark:border-slate-600 dark:bg-slate-800">
                                    Hapus foto saat ini
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Ketua Umum</label>
                    <input type="text" name="chairman_name" value="{{ old('chairman_name', $setting->chairman_name) }}"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                    @error('chairman_name')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Teks Sambutan</label>
                    <textarea name="chairman_message" rows="5"
                        placeholder="Tulis pesan sambutan Ketua Umum di sini..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('chairman_message', $setting->chairman_message) }}</textarea>
                    @error('chairman_message')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ============ MODE ASPIRASI ============ --}}
            <div class="theme-transition mt-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
                <h2 class="text-base font-bold text-slate-800 dark:text-white">Mode Formulir Aspirasi</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Atur siapa yang boleh mengirim pesan lewat halaman Kontak & Aspirasi.</p>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Mode Aspirasi</label>
                    <select name="aspiration_mode"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @foreach (\App\Models\HomeSetting::ASPIRATION_MODES as $value => $label)
                            <option value="{{ $value }}" {{ old('aspiration_mode', $setting->aspiration_mode) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="mt-3 space-y-1.5 text-xs text-slate-400 dark:text-slate-500">
                        <p><span class="font-semibold text-slate-500 dark:text-slate-400">Publik:</span> formulir terbuka untuk siapa saja, termasuk pengiriman anonim.</p>
                        <p><span class="font-semibold text-slate-500 dark:text-slate-400">Hanya Pengurus:</span> pengunjung wajib login sebagai pengurus untuk mengirim aspirasi internal.</p>
                        <p><span class="font-semibold text-slate-500 dark:text-slate-400">Nonaktif:</span> formulir disembunyikan sepenuhnya, hanya menampilkan info kontak & media sosial.</p>
                    </div>
                    @error('aspiration_mode')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse items-center justify-end gap-3 sm:flex-row">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20 sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
