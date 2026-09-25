@extends('layouts.app')

@section('title', 'Tambah Tamu - KATIBER')

@section('content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border dark:border-slate-700">
        <h2 class="text-xl font-bold mb-6 border-b dark:border-slate-700 pb-2 text-slate-800 dark:text-white">Form Input Tamu
        </h2>

        <form action="{{ route('guests.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-600 dark:text-slate-300 mb-1">Acara / Agenda yang
                    Dihadiri <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="agenda_id" required
                    class="w-full px-4 py-2 border dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-slate-900 dark:text-white appearance-none pr-10">
                    <option value="">-- Pilih Agenda --</option>
                    @foreach ($agendas as $agenda)
                        <option value="{{ $agenda->id }}">{{ \Carbon\Carbon::parse($agenda->date)->format('d/m/Y') }} -
                            {{ $agenda->name }}</option>
                    @endforeach
                </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
                @error('agenda_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Tamu <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="Contoh: Ir. H. Umar Zunaidi" required
                        class="w-full px-4 py-2 border dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-slate-900 dark:text-white dark:placeholder-slate-500">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 dark:text-slate-300 mb-1">Instansi / Organisasi
                        / Kelompok <span class="text-red-500">*</span></label>
                    <input type="text" name="institution" value="{{ old('institution') }}"
                        placeholder="Contoh: Pemko Tebing Tinggi / Alumni / IMASU" required
                        class="w-full px-4 py-2 border dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-slate-900 dark:text-white dark:placeholder-slate-500">
                    @error('institution')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 dark:text-slate-300 mb-1">Perwakilan / Jabatan
                        (Opsional)</label>
                    <input type="text" name="role" value="{{ old('role') }}"
                        placeholder="Contoh: Mewakili Walikota / Ketua Umum"
                        class="w-full px-4 py-2 border dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-slate-900 dark:text-white dark:placeholder-slate-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 dark:text-slate-300 mb-1">Kontak / No HP
                        (Opsional)</label>
                    <input type="text" name="contact" value="{{ old('contact') }}" placeholder="Contoh: 0812xxxxxx"
                        class="w-full px-4 py-2 border dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white dark:bg-slate-900 dark:text-white dark:placeholder-slate-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t dark:border-slate-700 pt-4">
                <a href="{{ route('guests.index') }}"
                    class="px-4 py-2 border dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Batal</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-sm">Simpan
                    Tamu</button>
            </div>
        </form>
    </div>
@endsection
