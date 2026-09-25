@extends('layouts.app')

@section('title', $kepanitiaan->name . ' - Kepanitiaan - KATIBER')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2">
                <a href="{{ route('kepanitiaan.index') }}" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4.5 w-4.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                    {{ $kepanitiaan->status === 'Selesai' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : ($kepanitiaan->status === 'Berjalan' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400') }}">
                    {{ $kepanitiaan->status }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $kepanitiaan->name }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $kepanitiaan->type ?? 'Tanpa jenis' }} ·
                @if ($kepanitiaan->start_date)
                    {{ $kepanitiaan->start_date->translatedFormat('d M Y') }}{{ $kepanitiaan->end_date ? ' - ' . $kepanitiaan->end_date->translatedFormat('d M Y') : '' }}
                @else
                    Tanggal belum ditentukan
                @endif
            </p>
            @if ($kepanitiaan->description)
                <p class="mt-2 max-w-2xl text-sm text-slate-600 dark:text-slate-300">{{ $kepanitiaan->description }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @can('manage_committees')
                <a href="{{ route('kepanitiaan.edit', $kepanitiaan->id) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Edit
                </a>
            @endcan
            @can('delete_committees')
                <form action="{{ route('kepanitiaan.destroy', $kepanitiaan->id) }}" method="POST" onsubmit="return confirm('Hapus kepanitiaan ini beserta seluruh datanya?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-500/30 dark:text-red-400 dark:hover:bg-red-500/10">Hapus</button>
                </form>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- ==================== KOLOM UTAMA: ANGGOTA PANITIA ==================== --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Teras Panitia --}}
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Teras Panitia</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($terasPanitia as $cm)
                        @include('committees._anggota-row', ['cm' => $cm])
                    @empty
                        <p class="px-5 py-6 text-sm text-slate-400 dark:text-slate-500">Belum ada Ketua/Sekretaris/Bendahara Panitia.</p>
                    @endforelse
                </div>
            </div>

            {{-- Anggota per Bidang --}}
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Anggota per Bidang</h2>
                </div>
                @forelse ($anggotaBidang as $bidangName => $rows)
                    <div class="border-b border-slate-100 px-5 py-3 dark:border-slate-800">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $bidangName }}</p>
                        <div class="divide-y divide-slate-50 dark:divide-slate-800/60">
                            @foreach ($rows as $cm)
                                @include('committees._anggota-row', ['cm' => $cm])
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-6 text-sm text-slate-400 dark:text-slate-500">Belum ada anggota bidang.</p>
                @endforelse
            </div>

            {{-- Form Tambah Anggota --}}
            @can('manage_committees')
                {{-- @js() (bukan @json()) dipakai untuk "members" di bawah karena x-data ini
                dibungkus tanda kutip DUA, sedangkan @json() menghasilkan JSON yang juga memakai
                kutip dua mentah — kalau dipakai, kutip dua di dalam JSON itu akan menutup
                atribut x-data lebih awal dan sisa kodenya bocor tampil sebagai teks di halaman.
                @js() meng-escape hasilnya supaya aman dipakai di atribut HTML kutip dua. --}}
                <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
                    x-data="{
                        mode: 'existing',
                        members: @js($availableMembers->map(fn ($m) => ['id' => $m->id, 'name' => $m->name, 'type' => $m->membership_type . ($m->student_id ? ' · ' . $m->student_id : '')])),
                        memberId: '',
                        memberSearch: '',
                        comboOpen: false,
                        quickName: '',
                        quickUniversity: '',
                        positionCategory: '',
                        customPosition: '',
                        submitting: false,
                        filteredMembers() {
                            const q = this.memberSearch.trim().toLowerCase();
                            if (q === '') { return this.members; }
                            return this.members.filter((m) => m.name.toLowerCase().includes(q));
                        },
                        selectMember(m) {
                            this.memberId = m.id;
                            this.memberSearch = m.name;
                            this.comboOpen = false;
                        },
                        finalPositionCategory() {
                            return this.positionCategory === '__custom__' ? this.customPosition.trim() : this.positionCategory;
                        },
                        async handleSubmit(e) {
                            if (this.positionCategory === '__custom__' && !this.customPosition.trim()) {
                                e.preventDefault();
                                alert('Ketik dulu nama perannya.');
                                return;
                            }
                            if (this.mode === 'new') {
                                e.preventDefault();
                                if (!this.quickName.trim()) { alert('Isi nama orangnya dulu.'); return; }
                                this.submitting = true;
                                try {
                                    const res = await fetch('{{ route('kepanitiaan.anggota.quick-create') }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                        body: JSON.stringify({ name: this.quickName, university: this.quickUniversity })
                                    });
                                    if (!res.ok) { throw new Error('Gagal menyimpan orang baru.'); }
                                    const data = await res.json();
                                    this.memberId = data.id;
                                    this.$nextTick(() => e.target.submit());
                                } catch (err) {
                                    alert(err.message);
                                    this.submitting = false;
                                }
                            }
                        }
                    }">
                    <h3 class="mb-4 text-sm font-bold text-slate-800 dark:text-white">Tambah Anggota Panitia</h3>
                    <form action="{{ route('kepanitiaan.anggota.store', $kepanitiaan->id) }}" method="POST" @submit="handleSubmit($event)">
                        @csrf
                        <input type="hidden" name="member_id" :value="memberId">
                        <input type="hidden" name="position_category" :value="finalPositionCategory()">

                        <div class="mb-4 flex gap-2">
                            <button type="button" @click="mode = 'existing'"
                                :class="mode === 'existing' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'"
                                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition">Pilih dari Daftar</button>
                            <button type="button" @click="mode = 'new'"
                                :class="mode === 'new' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'"
                                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition">+ Tambah Orang Baru</button>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="relative md:col-span-2" x-show="mode === 'existing'" @click.outside="comboOpen = false">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Orang</label>
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"
                                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <input type="text" x-model="memberSearch" @focus="comboOpen = true"
                                        @input="memberId = ''; comboOpen = true" autocomplete="off"
                                        placeholder="Cari nama orang..." :required="mode === 'existing'"
                                        class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                </div>
                                <div x-show="comboOpen" x-cloak
                                    class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                                    <template x-for="m in filteredMembers()" :key="m.id">
                                        <button type="button" @click="selectMember(m)"
                                            class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700">
                                            <span x-text="m.name"></span>
                                            <span class="text-xs text-slate-400" x-text="m.type"></span>
                                        </button>
                                    </template>
                                    <p x-show="filteredMembers().length === 0" class="px-3 py-2 text-xs text-slate-400">Tidak ditemukan. Coba kata kunci lain atau pakai "+ Tambah Orang Baru".</p>
                                </div>
                            </div>
                            <div x-show="mode === 'new'">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Orang Baru</label>
                                <input type="text" x-model="quickName" :required="mode === 'new'"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                <p class="mt-1 text-xs text-slate-400">Otomatis tersimpan sebagai Non-Pengurus.</p>
                            </div>
                            <div x-show="mode === 'new'">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Asal Kampus/Instansi (opsional)</label>
                                <input type="text" x-model="quickUniversity"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Peran</label>
                                <div class="relative">
                                    <select x-model="positionCategory" required
                                        class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                        <option value="">Pilih peran...</option>
                                        @foreach (\App\Models\Committee::positionCategories() as $pos)
                                            <option value="{{ $pos }}">{{ $pos }}</option>
                                        @endforeach
                                        <option value="__custom__">+ Lainnya (ketik sendiri)</option>
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                                <input type="text" x-show="positionCategory === '__custom__'" x-cloak x-model="customPosition"
                                    :required="positionCategory === '__custom__'" placeholder="Ketik peran, mis. Penanggung Jawab Lapangan"
                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </div>
                            <div x-show="positionCategory === 'Ketua Bidang' || positionCategory === 'Anggota Bidang'" x-cloak>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Bidang Panitia</label>
                                <div class="relative">
                                    <select name="panitia_bidang_id" :required="positionCategory === 'Ketua Bidang' || positionCategory === 'Anggota Bidang'"
                                        class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                        <option value="">Pilih bidang...</option>
                                        @foreach ($bidangList as $bidang)
                                            <option value="{{ $bidang->id }}">{{ $bidang->name }}</option>
                                        @endforeach
                                    </select>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                                @if ($bidangList->isEmpty())
                                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">Belum ada bidang panitia. <a href="{{ route('bidang-panitia.index') }}" class="underline">Tambahkan dulu di sini.</a></p>
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan (opsional)</label>
                                <input type="text" name="notes"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" :disabled="submitting"
                                class="rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md disabled:opacity-50">
                                <span x-show="!submitting">Tambahkan</span>
                                <span x-show="submitting">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endcan
        </div>

        {{-- ==================== SIDEBAR: AGENDA TERKAIT ==================== --}}
        <div class="space-y-6">
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white">Agenda Terkait</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Rapat persiapan & hari-H yang dipakai untuk menghitung statistik kehadiran panitia.</p>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($kepanitiaan->agendas as $agenda)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $agenda->name }}</p>
                                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($agenda->date)->translatedFormat('d M Y') }} · {{ $agenda->type }}</p>
                            </div>
                            @can('manage_committees')
                                <form action="{{ route('kepanitiaan.agenda.destroy', [$kepanitiaan->id, $agenda->id]) }}" method="POST" onsubmit="return confirm('Lepas agenda ini dari kepanitiaan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 dark:hover:bg-red-500/10">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @empty
                        <p class="px-5 py-6 text-sm text-slate-400 dark:text-slate-500">Belum ada agenda yang dikaitkan.</p>
                    @endforelse
                </div>
                @can('manage_committees')
                    <form action="{{ route('kepanitiaan.agenda.store', $kepanitiaan->id) }}" method="POST" class="border-t border-slate-100 p-4 dark:border-slate-800">
                        @csrf
                        <label class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-slate-400">Kaitkan Agenda</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <select name="agenda_id" required
                                    class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-10 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                    <option value="">Pilih agenda...</option>
                                    @foreach ($availableAgendas as $agenda)
                                        <option value="{{ $agenda->id }}">{{ $agenda->name }} ({{ \Carbon\Carbon::parse($agenda->date)->format('d/m/Y') }})</option>
                                    @endforeach
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                            <button type="submit" class="shrink-0 rounded-lg bg-slate-800 px-3.5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900">+</button>
                        </div>
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endsection
