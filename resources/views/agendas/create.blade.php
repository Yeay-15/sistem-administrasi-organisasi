@extends('layouts.app')

@section('title', 'Tambah Agenda - KATIBER')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('agendas.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Tambah Agenda Baru</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Jadwalkan rapat, pleno, atau kegiatan organisasi.</p>
            </div>
        </div>

        <div
            class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <form action="{{ route('agendas.store') }}" method="POST" autocomplete="off" x-data="{
                pic: '{{ old('person_in_charge') }}',
                code: '{{ old('agenda_code') }}',
                collabDivisions: [],
                prefixes: {
                    @foreach ($divisions as $div)
                        '{{ $div->name }}': '{{ strtoupper($div->abbreviation) }}-', @endforeach 'Kolaborasi / Lintas Divisi': 'KOLAB-'
                },
                updatePrefix() {
                    if (this.prefixes[this.pic]) {
                        let isPristine = this.code === '' || Object.values(this.prefixes).includes(this.code);
                        if (isPristine) {
                            this.code = this.prefixes[this.pic];
                        }
                    }
                },
                get finalPic() {
                    if (this.pic === 'Kolaborasi / Lintas Divisi' && this.collabDivisions.length > 0) {
                        return this.collabDivisions.join(' x ');
                    }
                    return this.pic;
                }
            }">
                @csrf

                <!-- Hidden input untuk menyimpan hasil gabungan PIC -->
                <input type="hidden" name="person_in_charge" :value="finalPic">

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Kode
                            Agenda</label>
                        <input type="text" x-model="code" name="agenda_code" placeholder="Contoh: KADER-001" required
                            autocomplete="off"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('agenda_code')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama
                            Agenda</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Contoh: Rapat Evaluasi Proker" required autocomplete="off"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal</label>
                        <input type="date" name="date" value="{{ old('date') }}" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('date')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jenis
                            Agenda</label>
                        <select name="type" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Rapat Internal" {{ old('type') == 'Rapat Internal' ? 'selected' : '' }}>Rapat
                                Internal</option>
                            <option value="Kegiatan / Event" {{ old('type') == 'Kegiatan / Event' ? 'selected' : '' }}>
                                Kegiatan / Event</option>
                            <option value="Pleno / Muskom" {{ old('type') == 'Pleno / Muskom' ? 'selected' : '' }}>Pleno /
                                Muskom</option>
                            <option value="Lainnya" {{ old('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('type')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Status
                            Agenda</label>
                        <select name="status" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="Terjadwal" {{ old('status') == 'Terjadwal' ? 'selected' : '' }}>Terjadwal
                            </option>
                            <option value="Selesai" {{ old('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ old('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Penanggung Jawab
                        (PIC)</label>
                    <select x-model="pic" @change="updatePrefix()" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Pilih Penanggung Jawab / Divisi --</option>
                        @foreach ($divisions as $div)
                            <option value="{{ $div->name }}">{{ $div->name }}</option>
                        @endforeach
                        <option value="Kolaborasi / Lintas Divisi">Kolaborasi / Lintas Divisi</option>
                    </select>
                    @error('person_in_charge')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Menu Checklist Muncul Otomatis Jika Pilih Kolaborasi -->
                <div x-show="pic === 'Kolaborasi / Lintas Divisi'" x-cloak
                    class="mt-4 rounded-xl border border-blue-100 bg-blue-50/50 p-4 dark:border-blue-900/30 dark:bg-blue-500/5">
                    <p class="mb-2.5 text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Pilih
                        Divisi yang Berkolaborasi:</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($divisions as $div)
                            <label
                                class="flex items-center gap-2.5 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                <input type="checkbox" value="{{ $div->name }}" x-model="collabDivisions"
                                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800">
                                <span class="truncate">{{ $div->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 italic">Hasil PIC otomatis: <span
                            class="font-semibold text-blue-600 dark:text-blue-400"
                            x-text="finalPic || '(Belum ada divisi yang dicentang)'"></span></p>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Keterangan
                        (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Keterangan tambahan..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <a href="{{ route('agendas.index') }}"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                        Simpan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
