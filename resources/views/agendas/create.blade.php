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
                attendanceScope: '{{ old('attendance_scope', 'all') }}',
                attendanceDivisions: {{ json_encode(old('division_ids', [])) }},
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
                        <x-dropdown-select name="type" placeholder="-- Pilih Jenis --" :required="true"
                            :selected="old('type')" :options="[
                                'Rapat Internal' => 'Rapat Internal',
                                'Kegiatan / Event' => 'Kegiatan / Event',
                                'Pleno / Muskom' => 'Pleno / Muskom',
                                'Lainnya' => 'Lainnya',
                            ]" />
                        @error('type')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Status
                            Agenda</label>
                        <x-dropdown-select name="status" placeholder="-- Pilih Status --" :required="true"
                            :selected="old('status', 'Terjadwal')" :options="[
                                'Terjadwal' => 'Terjadwal',
                                'Selesai' => 'Selesai',
                                'Dibatalkan' => 'Dibatalkan',
                            ]" />
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Penanggung Jawab
                        (PIC)</label>
                    <x-dropdown-select placeholder="-- Pilih Penanggung Jawab / Divisi --" :required="true"
                        :selected="old('person_in_charge')" on-select="pic = value; updatePrefix()"
                        :options="$divisions->pluck('name', 'name')->put('Kolaborasi / Lintas Divisi', 'Kolaborasi / Lintas Divisi')" />
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
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Cakupan
                        Absensi</label>
                    <p class="mb-2.5 text-xs text-slate-500 dark:text-slate-400">Menentukan siapa saja yang muncul di
                        daftar absensi & terhitung di statistik kehadirannya untuk agenda ini.</p>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                            :class="attendanceScope === 'all' ? 'border-blue-500 bg-blue-50/60 dark:border-blue-500 dark:bg-blue-500/10' : 'border-slate-200 dark:border-slate-700'">
                            <input type="radio" name="attendance_scope" value="all" x-model="attendanceScope"
                                class="mt-0.5 h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                            <span>
                                <span class="block text-sm font-semibold text-slate-800 dark:text-white">Seluruh
                                    Pengurus</span>
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">Semua pengurus
                                    aktif diabsen & masuk statistik kehadiran mereka.</span>
                            </span>
                        </label>
                        <label
                            class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                            :class="attendanceScope === 'division' ? 'border-blue-500 bg-blue-50/60 dark:border-blue-500 dark:bg-blue-500/10' : 'border-slate-200 dark:border-slate-700'">
                            <input type="radio" name="attendance_scope" value="division" x-model="attendanceScope"
                                class="mt-0.5 h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                            <span>
                                <span class="block text-sm font-semibold text-slate-800 dark:text-white">Divisi
                                    Tertentu</span>
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">Hanya anggota
                                    divisi terpilih yang diabsen. Divisi lain tidak ikut dinilai kehadirannya di
                                    agenda ini.</span>
                            </span>
                        </label>
                    </div>
                    @error('attendance_scope')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <div x-show="attendanceScope === 'division'" x-cloak
                        class="mt-3 rounded-xl border border-blue-100 bg-blue-50/50 p-4 dark:border-blue-900/30 dark:bg-blue-500/5">
                        <p class="mb-2.5 text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                            Pilih Divisi yang Diabsen:</p>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($divisions as $div)
                                <label
                                    class="flex items-center gap-2.5 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                    <input type="checkbox" name="division_ids[]" value="{{ $div->id }}"
                                        x-model="attendanceDivisions"
                                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800">
                                    <span class="truncate">{{ $div->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('division_ids')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Keterangan
                        (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Keterangan tambahan..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-5 flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/40">
                    <input type="checkbox" id="is_public" name="is_public" value="1"
                        {{ old('is_public', true) ? 'checked' : '' }}
                        class="mt-0.5 h-4.5 w-4.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-800">
                    <label for="is_public" class="cursor-pointer text-sm">
                        <span class="block font-medium text-slate-700 dark:text-slate-300">Tampilkan ke publik</span>
                        <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">Jika dicentang, agenda ini muncul di kalender Portal Publik. Matikan untuk agenda internal seperti rapat pembentukan panitia — tetap tercatat di kalender pengurus.</span>
                    </label>
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
