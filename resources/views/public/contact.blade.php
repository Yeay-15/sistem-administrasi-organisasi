@extends('layouts.public')

@section('title', 'Kontak & Aspirasi - KATIBER')

@section('content')
    <section class="border-b border-slate-100 bg-gradient-to-b from-blue-50 to-white dark:border-slate-800 dark:from-slate-900 dark:to-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white sm:text-4xl">Kontak & Aspirasi</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-slate-500 dark:text-slate-400">
                Suaramu berharga. Sampaikan pendapat, kritik, saran, atau pertanyaanmu langsung di sini.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-2xl px-4 py-14 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        @if ($settings->aspiration_mode === 'nonaktif')
            {{-- ============ MODE: NONAKTIF ============ --}}
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="bg-gradient-to-br from-slate-600 to-slate-700 px-6 py-8 text-center sm:px-8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-10 w-10 text-white/80">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <h2 class="mt-3 text-lg font-bold text-white">Formulir Aspirasi Sedang Nonaktif</h2>
                    <p class="mt-1 text-sm text-slate-200">Untuk sementara, silakan hubungi kami langsung melalui kanal berikut.</p>
                </div>
                <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-3 sm:p-8">
                    <a href="https://instagram.com" target="_blank" class="flex flex-col items-center gap-2 rounded-xl border border-slate-100 p-4 text-center transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-pink-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.174C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.174 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                        </svg>
                        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Instagram</span>
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="flex flex-col items-center gap-2 rounded-xl border border-slate-100 p-4 text-center transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-emerald-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.5-1.185A8.959 8.959 0 013 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                        </svg>
                        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">WhatsApp</span>
                    </a>
                    <a href="mailto:katiber@gmail.com" class="flex flex-col items-center gap-2 rounded-xl border border-slate-100 p-4 text-center transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-blue-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Email</span>
                    </a>
                </div>
            </div>
        @elseif ($settings->aspiration_mode === 'pengurus_only' && ! auth()->check())
            {{-- ============ MODE: HANYA PENGURUS, BELUM LOGIN ============ --}}
            <div class="theme-transition overflow-hidden rounded-2xl border border-amber-200 bg-white p-8 text-center shadow-sm dark:border-amber-500/30 dark:bg-slate-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-10 w-10 text-amber-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <h2 class="mt-3 text-lg font-bold text-slate-800 dark:text-white">Aspirasi Internal Pengurus</h2>
                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500 dark:text-slate-400">
                    Silakan login sebagai pengurus untuk mengirim aspirasi internal.
                </p>
                <a href="{{ route('login') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                    Login Pengurus
                </a>
            </div>
        @else
            {{-- ============ MODE: PUBLIK / PENGURUS SUDAH LOGIN ============ --}}
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
                x-data="{ anonymous: false, category: 'Aspirasi' }">
                <div class="bg-gradient-to-br from-blue-600 to-indigo-600 px-6 py-6 sm:px-8">
                    <h2 class="text-lg font-bold text-white">Formulir Aspirasi Mahasiswa</h2>
                    <p class="mt-1 text-sm text-blue-100">Kritik, saran, dan laporan Anda membantu kami berkembang.</p>
                    @if ($settings->aspiration_mode === 'pengurus_only')
                        <span class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">Mode Internal Pengurus</span>
                    @endif
                </div>

                <form action="{{ route('public.contact.store') }}" method="POST" class="p-6 sm:p-8">
                    @csrf

                    @if ($settings->aspiration_mode !== 'pengurus_only')
                        <div class="mb-5 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/60">
                            <div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Mode Privasi</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Sembunyikan identitas Anda?</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" name="is_anonymous" value="1" x-model="anonymous" class="peer sr-only">
                                <div class="peer h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-blue-600 dark:bg-slate-700
                                    after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full">
                                </div>
                                <span class="ml-2 text-xs font-medium text-slate-500 dark:text-slate-400">Kirim Anonim</span>
                            </label>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2" x-show="!anonymous" x-cloak>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Masukkan namamu..."
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kontak (WA/Email)</label>
                            <input type="text" name="contact" value="{{ old('contact') }}" placeholder="Nomor WA atau Email aktif..."
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kategori Pesan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach (['Aspirasi', 'Kritik Saran', 'Laporan', 'Pertanyaan'] as $cat)
                                <label class="flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border px-3 py-2.5 text-xs font-semibold transition"
                                    :class="category === '{{ $cat }}' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800'">
                                    <input type="radio" name="category" value="{{ $cat }}" x-model="category" class="sr-only" {{ $cat === 'Aspirasi' ? 'checked' : '' }}>
                                    {{ $cat }}
                                </label>
                            @endforeach
                        </div>
                        @error('category')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-5">
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Isi Pesan <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="5" required placeholder="Tuliskan aspirasi, kritik, atau pertanyaanmu di sini secara detail..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-amber-400 to-amber-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:shadow-md">
                        Kirim Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>

                    <p class="mt-3 text-center text-xs text-slate-400 dark:text-slate-500">
                        Privasi Anda terjaga. Pesan akan langsung diterima oleh Pengurus KATIBER.
                    </p>
                </form>
            </div>
        @endif
    </section>
@endsection
