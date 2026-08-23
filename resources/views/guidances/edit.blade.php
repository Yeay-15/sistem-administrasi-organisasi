@extends('layouts.app')

@section('title', 'Edit Pembinaan - KATIBER')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('guidances.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Edit Data Pembinaan</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Perbarui catatan pendisiplinan pengurus.</p>
            </div>
        </div>

        <div
            class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <form action="{{ route('guidances.update', $guidance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <!-- Komponen Searchable Dropdown Alpine.js (Diperbaiki) -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Pengurus yang
                            Dibina</label>

                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('member_id', $guidance->member_id) }}',
                            options: {{ Js::from(collect($members)->map(function ($m) {return ['id' => $m->id, 'label' => $m->name . ' (' . $m->student_id . ')'];})->values()) }},
                            get filteredOptions() {
                                if (this.search === '') return this.options;
                                return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            get selectedLabel() {
                                const opt = this.options.find(o => o.id == this.selectedId);
                                return opt ? opt.label : '-- Cari & Pilih Pengurus --';
                            },
                            selectOption(id) {
                                this.selectedId = id;
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative w-full" @keydown.escape.prevent.stop="open = false"
                            @click.outside="open = false" x-init="$watch('open', value => { if (value) setTimeout(() => $refs.searchInput.focus(), 50) })">

                            <input type="hidden" name="member_id" :value="selectedId">

                            <button type="button" @click="open = !open"
                                class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                :class="{ 'ring-2 ring-blue-500 border-blue-500': open, 'text-slate-800 dark:text-white': selectedId, 'text-slate-500 dark:text-slate-400':
                                        !selectedId }">
                                <span x-text="selectedLabel" class="truncate"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition.opacity x-cloak
                                class="absolute z-50 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                                    <input type="text" x-model="search" x-ref="searchInput"
                                        placeholder="Ketik nama atau NIM..."
                                        class="w-full rounded-md border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500"
                                        @keydown.enter.prevent="if(filteredOptions.length > 0) selectOption(filteredOptions[0].id)">
                                </div>

                                <ul class="max-h-60 overflow-y-auto py-1">
                                    <template x-for="option in filteredOptions" :key="option.id">
                                        <li @click="selectOption(option.id)"
                                            class="cursor-pointer px-3.5 py-2 text-sm transition-colors"
                                            :class="selectedId == option.id ?
                                                'bg-blue-50 text-blue-700 font-semibold dark:bg-blue-500/10 dark:text-blue-400' :
                                                'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/50'">
                                            <span x-text="option.label"></span>
                                        </li>
                                    </template>
                                    <li x-show="filteredOptions.length === 0"
                                        class="px-3.5 py-3 text-center text-sm text-slate-400 dark:text-slate-500">
                                        Pengurus tidak ditemukan
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @error('member_id')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal
                            Pembinaan</label>
                        <input type="date" name="date" value="{{ old('date', $guidance->date) }}" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('date')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Jenis
                            Pembinaan</label>
                        <select name="type" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="Teguran Lisan"
                                {{ old('type', $guidance->type) == 'Teguran Lisan' ? 'selected' : '' }}>Teguran Lisan
                            </option>
                            <option value="Teguran Tertulis"
                                {{ old('type', $guidance->type) == 'Teguran Tertulis' ? 'selected' : '' }}>Teguran Tertulis
                            </option>
                            <option value="SP 1" {{ old('type', $guidance->type) == 'SP 1' ? 'selected' : '' }}>Surat
                                Peringatan 1</option>
                            <option value="SP 2" {{ old('type', $guidance->type) == 'SP 2' ? 'selected' : '' }}>Surat
                                Peringatan 2</option>
                            <option value="SP 3" {{ old('type', $guidance->type) == 'SP 3' ? 'selected' : '' }}>Surat
                                Peringatan 3</option>
                            <option value="Lainnya" {{ old('type', $guidance->type) == 'Lainnya' ? 'selected' : '' }}>
                                Lainnya</option>
                        </select>
                        @error('type')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Pemberi
                            Pembinaan</label>
                        <input type="text" name="counselor" value="{{ old('counselor', $guidance->counselor) }}"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                        @error('counselor')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                        <select name="status" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="Berlaku" {{ old('status', $guidance->status) == 'Berlaku' ? 'selected' : '' }}>
                                Berlaku</option>
                            <option value="Selesai" {{ old('status', $guidance->status) == 'Selesai' ? 'selected' : '' }}>
                                Selesai</option>
                            <option value="Dicabut" {{ old('status', $guidance->status) == 'Dicabut' ? 'selected' : '' }}>
                                Dicabut</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Alasan /
                        Pelanggaran</label>
                    <input type="text" name="reason" value="{{ old('reason', $guidance->reason) }}" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                    @error('reason')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Keterangan Tambahan /
                        Hasil</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('notes', $guidance->notes) }}</textarea>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <a href="{{ route('guidances.index') }}"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Batal</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Perbarui Data Pembinaan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
