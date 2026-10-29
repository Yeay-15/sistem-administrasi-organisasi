@extends('layouts.app')

@section('title', $template->name . ' - Surat Generator - KATIBER')

@php
    $inputClass = 'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]';
    $card = 'theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8';
    $sectionTitle = 'mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400';
@endphp

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ $backUrl }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">{{ $template->name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    @if ($committee) Kepanitiaan: <span class="font-semibold">{{ $committee->name }}</span> &middot; @endif
                    Hasilnya berupa file Word (.docx) dan otomatis tercatat di Surat Keluar sebagai Draft.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
                Ada isian yang belum benar. Periksa kolom yang ditandai merah.
            </div>
        @endif

        <form action="{{ $action }}" method="POST" class="space-y-6">
            @csrf

            @if (count($templateFields))
                <div class="{{ $card }}">
                    <h2 class="{{ $sectionTitle }}">Isi Surat</h2>
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        @foreach ($templateFields as $field)
                            @include('letter_generator._field', ['field' => $field, 'defaults' => $defaults])
                        @endforeach
                    </div>
                </div>
            @endif

            @if (count($eventFields))
                <div class="{{ $card }}">
                    <h2 class="{{ $sectionTitle }}">Data Kegiatan</h2>
                    <p class="-mt-2 mb-4 text-xs text-slate-500 dark:text-slate-400">
                        Diambil dari data kepanitiaan. Mengubahnya di sini hanya berlaku untuk surat ini.
                    </p>
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        @foreach ($eventFields as $field)
                            @include('letter_generator._field', ['field' => $field, 'defaults' => $defaults])
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="{{ $card }}">
                <h2 class="{{ $sectionTitle }}">Nomor &amp; Tanggal Surat</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Urut</label>
                        <input type="number" min="1" name="nomor_urut" value="{{ old('nomor_urut', $defaults['nomor_urut']) }}" required class="{{ $inputClass }}">
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Otomatis lanjut dari surat keluar tahun ini. Ubah manual jika tanggal surat beda tahun.</p>
                        @error('nomor_urut')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', $defaults['tanggal_surat']) }}" required class="{{ $inputClass }}">
                        @error('tanggal_surat')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Kota</label>
                        <input type="text" name="kota" value="{{ old('kota', $defaults['kota']) }}" required class="{{ $inputClass }}">
                        @error('kota')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <h2 class="{{ $sectionTitle }}">Penandatangan</h2>
                <div class="space-y-5">
                    @foreach ($template->signers as $role)
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-3 md:items-end">
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $signerLabels[$role] ?? $role }} &dash; Nama</label>
                                <input type="text" name="{{ $role }}_nama" value="{{ old($role . '_nama', $defaults[$role . '_nama'] ?? '') }}" required class="{{ $inputClass }}">
                                @error($role . '_nama')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">NIM</label>
                                <input type="text" name="{{ $role }}_nim" value="{{ old($role . '_nim', $defaults[$role . '_nim'] ?? '') }}" required class="{{ $inputClass }}">
                                @error($role . '_nim')<p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    Terisi dari struktur panitia{{ $committee ? ' (Ketua & Sekretaris Panitia)' : '' }} dan pengurus aktif (Ketua/Sekretaris Umum). Kosong berarti jabatan itu belum diisi di data; ketik manual.
                </p>
            </div>

            @error('format')<p class="text-right text-sm text-red-500 dark:text-red-400">{{ $message }}</p>@enderror
            <div class="flex justify-end gap-3">
                <a href="{{ $backUrl }}"
                    class="rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                @if ($pdfAvailable)
                    <button type="submit" name="format" value="pdf"
                        class="rounded-lg border border-blue-200 px-5 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-50 dark:border-blue-500/40 dark:text-blue-300 dark:hover:bg-blue-500/10">
                        Buat &amp; Unduh PDF
                    </button>
                @endif
                <button type="submit" name="format" value="docx"
                    class="rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md">
                    Buat &amp; Unduh Word
                </button>
            </div>
        </form>
    </div>
@endsection
