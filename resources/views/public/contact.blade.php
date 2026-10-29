@extends('layouts.public')

@section('title', 'Kontak & Aspirasi - KATIBER')
@section('meta_description', 'Sampaikan pendapat, kritik, saran, atau pertanyaanmu langsung ke Pengurus KATIBER -
    Keluarga Mahasiswa Tebing Tinggi Bersatu.')

@section('content')
    @php
        // Formulir tampil: mode publik (semua orang) ATAU mode pengurus_only tapi sudah login.
        $showForm =
            $settings->aspiration_mode === 'public' ||
            ($settings->aspiration_mode === 'pengurus_only' && auth()->check());
    @endphp
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950">
        <div class="navy-dot-pattern absolute inset-0"></div>
        <div class="relative mx-auto max-w-6xl px-4 py-20 text-center sm:px-6 lg:px-8">
            <span
                class="reveal inline-block rounded-full bg-white/10 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-white ring-1 ring-inset ring-white/15">{{ $showForm ? 'Suaramu Berharga' : 'Hubungi Kami' }}</span>
            <h1 class="reveal mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-5xl" style="--reveal-delay:80ms">
                Kontak & Aspirasi</h1>
            <p class="reveal mx-auto mt-4 max-w-2xl text-base leading-relaxed text-navy-100 sm:text-lg"
                style="--reveal-delay:160ms">
                @if ($showForm)
                    Sampaikan pendapat, kritik, saran, atau pertanyaanmu langsung di sini.
                @else
                    Hubungi kami melalui kanal resmi KATIBER berikut.
                @endif
            </p>
        </div>
    </section>

    <section class="mx-auto {{ $showForm ? 'max-w-2xl' : 'max-w-4xl' }} px-4 py-14 sm:px-6 lg:px-8">
        @if (session('success'))
            <div
                class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        {{-- Pengunjung yang belum login pada mode 'pengurus_only', dan semua
             pengunjung pada mode 'nonaktif', tidak melihat formulir maupun
             kartu "Login Pengurus". --}}
        @if ($showForm)
            {{-- ============ FORMULIR (publik / pengurus login) ============ --}}
            <div class="reveal theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
                x-data="{ anonymous: false, category: 'Aspirasi' }">
                <div class="bg-gradient-to-br from-navy-700 to-navy-950 px-6 py-6 sm:px-8">
                    <h2 class="text-lg font-bold text-white">Formulir Aspirasi Mahasiswa</h2>
                    <p class="mt-1 text-sm text-navy-100">Kritik, saran, dan laporan Anda membantu kami berkembang.</p>
                    @if ($settings->aspiration_mode === 'pengurus_only')
                        <span
                            class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">Mode
                            Internal Pengurus</span>
                    @endif
                </div>

                <form action="{{ route('public.contact.store') }}" method="POST" class="p-6 sm:p-8">
                    @csrf

                    <div
                        class="mb-5 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/60">
                        <div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Mode Privasi</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Sembunyikan identitas Anda?</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="checkbox" name="is_anonymous" value="1" x-model="anonymous"
                                class="peer sr-only">
                            <div
                                class="peer h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-navy-700 dark:bg-slate-700
                                after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full">
                            </div>
                            <span class="ml-2 text-xs font-medium text-slate-500 dark:text-slate-400">Kirim
                                Anonim</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2" x-show="!anonymous" x-cloak>
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama
                                Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}"
                                placeholder="Masukkan namamu..."
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kontak
                                (WA/Email)</label>
                            <input type="text" name="contact" value="{{ old('contact') }}"
                                placeholder="Nomor WA atau Email aktif..."
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        </div>
                    </div>

                    <div class="mt-5">
                        <label
                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kategori
                            Pesan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach (['Aspirasi', 'Kritik Saran', 'Laporan', 'Pertanyaan'] as $cat)
                                <label
                                    class="flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border px-3 py-2.5 text-xs font-semibold transition"
                                    :class="category === '{{ $cat }}' ? 'border-navy-700 bg-navy-700 text-white' :
                                        'border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800'">
                                    <input type="radio" name="category" value="{{ $cat }}" x-model="category"
                                        class="sr-only" {{ $cat === 'Aspirasi' ? 'checked' : '' }}>
                                    {{ $cat }}
                                </label>
                            @endforeach
                        </div>
                        @error('category')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-5">
                        <label
                            class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Isi
                            Pesan <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="5" required
                            placeholder="Tuliskan aspirasi, kritik, atau pertanyaanmu di sini secara detail..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-br from-amber-400 to-amber-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:shadow-md">
                        Kirim Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>

                    <p class="mt-3 text-center text-xs text-slate-400 dark:text-slate-500">
                        Privasi Anda terjaga. Pesan akan langsung diterima oleh Pengurus KATIBER.
                    </p>
                </form>
            </div>
        @endif

        @if ($showForm)
            {{-- ============ KANAL RESMI LAINNYA ============
             Selalu tampil apa pun mode formulirnya, dan selalu sinkron
             dengan Pengaturan Beranda di dashboard admin (bukan cuma footer). --}}
            @if ($homeSettings->instagram_url || $homeSettings->whatsapp_link || $homeSettings->contact_email)
                <div class="reveal {{ $showForm ? 'mt-10' : 'mt-0' }} text-center">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ $showForm ? 'Kanal Resmi Lainnya' : 'Hubungi Kami' }}</p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        @if ($homeSettings->instagram_url)
                            <a href="{{ $homeSettings->instagram_url }}" target="_blank" aria-label="Instagram KATIBER"
                                class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5">
                                    <defs>
                                        <linearGradient id="ig-grad-2" x1="0%" y1="100%" x2="100%"
                                            y2="0%">
                                            <stop offset="0%" stop-color="#fd5949" />
                                            <stop offset="50%" stop-color="#d6249f" />
                                            <stop offset="100%" stop-color="#285AEB" />
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#ig-grad-2)"
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                                </svg>
                            </a>
                        @endif
                        @if ($homeSettings->whatsapp_link)
                            <a href="{{ $homeSettings->whatsapp_link }}" target="_blank" aria-label="WhatsApp KATIBER"
                                class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#25D366"
                                    class="h-5 w-5">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </a>
                        @endif
                        @if ($homeSettings->contact_email)
                            <a href="{{ $homeSettings->email_link }}"
                                @if ($homeSettings->email_link_is_web) target="_blank" rel="noopener" @endif
                                aria-label="Email KATIBER"
                                class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5">
                                    <path fill="#EA4335"
                                        d="M12 13.5L2 7c.4-1.2 1.5-2 2.8-2h14.4c1.3 0 2.4.8 2.8 2L12 13.5z" />
                                    <path fill="#34A853" d="M2 7v10.2C2 18.7 3.3 20 4.8 20H8v-7.5L2 7.7V7z" />
                                    <path fill="#4285F4" d="M22 7v10.2c0 1.5-1.3 2.8-2.8 2.8H16v-7.5l6-4.8V7z" />
                                    <path fill="#FBBC04" d="M8 12.5v7.5h8v-7.5L12 15.5 8 12.5z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        @else
            {{-- ============ KARTU KONTAK (formulir tidak tampil) ============
                 Ditampilkan besar seperti kartu navigasi Profil agar halaman
                 tetap terasa utuh saat formulir nonaktif / khusus pengurus. --}}
            @php
                $igHandle = null;
                if ($homeSettings->instagram_url) {
                    $igPath = trim((string) parse_url($homeSettings->instagram_url, PHP_URL_PATH), '/');
                    $igHandle = $igPath !== '' ? '@' . $igPath : 'Instagram KATIBER';
                }
                $channelCount = collect([
                    $homeSettings->instagram_url,
                    $homeSettings->whatsapp_link,
                    $homeSettings->contact_email,
                ])
                    ->filter()
                    ->count();
                $gridCols =
                    [1 => 'sm:grid-cols-1 sm:max-w-xs', 2 => 'sm:grid-cols-2 sm:max-w-2xl', 3 => 'sm:grid-cols-3'][
                        $channelCount
                    ] ?? 'sm:grid-cols-3';
            @endphp

            @if ($channelCount > 0)
                <div class="reveal text-center">
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white">Terhubung dengan KATIBER</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500 dark:text-slate-400">
                        Pilih kanal resmi yang paling nyaman untukmu.
                    </p>
                </div>

                <div class="reveal mx-auto mt-8 grid grid-cols-1 gap-6 {{ $gridCols }}" style="--reveal-delay:80ms">
                    @if ($homeSettings->instagram_url)
                        <a href="{{ $homeSettings->instagram_url }}" target="_blank" rel="noopener"
                            class="group flex flex-col items-center rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-amber-900/50">
                            <div
                                class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 transition-colors duration-300 group-hover:bg-amber-50 dark:bg-slate-800 dark:group-hover:bg-amber-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8">
                                    <defs>
                                        <linearGradient id="ig-grad-3" x1="0%" y1="100%" x2="100%"
                                            y2="0%">
                                            <stop offset="0%" stop-color="#fd5949" />
                                            <stop offset="50%" stop-color="#d6249f" />
                                            <stop offset="100%" stop-color="#285AEB" />
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#ig-grad-3)"
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                                </svg>
                            </div>
                            <h3
                                class="text-base font-semibold text-slate-800 transition-colors group-hover:text-amber-600 dark:text-white dark:group-hover:text-amber-400">
                                Instagram</h3>
                            <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">{{ $igHandle }}</p>
                            <span
                                class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-400">Kunjungi
                                profil <span aria-hidden="true"
                                    class="transition-transform group-hover:translate-x-1">&rarr;</span></span>
                        </a>
                    @endif

                    @if ($homeSettings->whatsapp_link)
                        <a href="{{ $homeSettings->whatsapp_link }}" target="_blank" rel="noopener"
                            class="group flex flex-col items-center rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-amber-900/50">
                            <div
                                class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 transition-colors duration-300 group-hover:bg-amber-50 dark:bg-slate-800 dark:group-hover:bg-amber-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#25D366"
                                    class="h-8 w-8">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </div>
                            <h3
                                class="text-base font-semibold text-slate-800 transition-colors group-hover:text-amber-600 dark:text-white dark:group-hover:text-amber-400">
                                WhatsApp</h3>
                            <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">
                                {{ $homeSettings->whatsapp_number ?: 'Chat langsung via WhatsApp' }}</p>
                            <span
                                class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-400">Chat
                                sekarang <span aria-hidden="true"
                                    class="transition-transform group-hover:translate-x-1">&rarr;</span></span>
                        </a>
                    @endif

                    @if ($homeSettings->contact_email)
                        <a href="{{ $homeSettings->email_link }}"
                            @if ($homeSettings->email_link_is_web) target="_blank" rel="noopener" @endif
                            class="group flex flex-col items-center rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-amber-900/50">
                            <div
                                class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 transition-colors duration-300 group-hover:bg-amber-50 dark:bg-slate-800 dark:group-hover:bg-amber-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-8 w-8">
                                    <path fill="#EA4335"
                                        d="M12 13.5L2 7c.4-1.2 1.5-2 2.8-2h14.4c1.3 0 2.4.8 2.8 2L12 13.5z" />
                                    <path fill="#34A853" d="M2 7v10.2C2 18.7 3.3 20 4.8 20H8v-7.5L2 7.7V7z" />
                                    <path fill="#4285F4" d="M22 7v10.2c0 1.5-1.3 2.8-2.8 2.8H16v-7.5l6-4.8V7z" />
                                    <path fill="#FBBC04" d="M8 12.5v7.5h8v-7.5L12 15.5 8 12.5z" />
                                </svg>
                            </div>
                            <h3
                                class="text-base font-semibold text-slate-800 transition-colors group-hover:text-amber-600 dark:text-white dark:group-hover:text-amber-400">
                                Email Resmi</h3>
                            <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">
                                {{ $homeSettings->contact_email }}</p>
                            <span
                                class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-400">Kirim
                                email <span aria-hidden="true"
                                    class="transition-transform group-hover:translate-x-1">&rarr;</span></span>
                        </a>
                    @endif
                </div>
            @endif
        @endif
    </section>
@endsection
