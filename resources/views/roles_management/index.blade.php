@extends('layouts.app')

@section('title', 'Manajemen Peran & Akses - KATIBER')

@section('content')
    <div x-data="rolesManagement()" @notice="showNotice($event.detail.message, $event.detail.type)" class="space-y-8">

        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen Peran & Akses</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Atur hak akses tiap peran, hak akses ekstra per divisi, lalu petakan akun login ke peran &amp; data pengurus
                yang sesuai.
            </p>
        </div>

        {{-- Notifikasi hasil aksi (toggle / update role) --}}
        <div x-show="notice" x-cloak x-transition.opacity
            :class="noticeType === 'error'
                ?
                'border-red-200 bg-red-50 text-red-700 dark:border-red-800/60 dark:bg-red-500/10 dark:text-red-400' :
                'border-green-200 bg-green-50 text-green-700 dark:border-green-800/60 dark:bg-green-500/10 dark:text-green-400'"
            class="theme-transition flex items-start justify-between gap-3 rounded-xl border px-4 py-3 text-sm">
            <span x-text="notice"></span>
            <button type="button" @click="notice = ''"
                class="shrink-0 text-xs font-semibold underline opacity-70 hover:opacity-100">Tutup</button>
        </div>

        {{-- Sandi awal akun baru - sengaja ditampilkan lewat flash session (bukan AJAX)
             supaya tetap terlihat jelas meski halaman ini reload setelah submit form. --}}
        @if (session('generated_account'))
            <div x-data="{ show: true }" x-show="show" x-cloak x-transition.opacity
                class="theme-transition flex items-start justify-between gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800/60 dark:bg-green-500/10 dark:text-green-400">
                <span>
                    Akun untuk <strong>{{ session('generated_account')['name'] }}</strong> berhasil dibuat.
                    Email: <strong>{{ session('generated_account')['email'] }}</strong> -
                    Sandi awal: <strong>{{ session('generated_account')['password'] }}</strong>
                    - segera sampaikan langsung ke pengurus yang bersangkutan, sandi ini tidak akan ditampilkan lagi.
                </span>
                <button type="button" @click="show = false"
                    class="shrink-0 text-xs font-semibold underline opacity-70 hover:opacity-100">Tutup</button>
            </div>
        @endif

        {{-- ============ BAGIAN 1: HAK AKSES PER PERAN ============ --}}
        <div x-data="roleAccess()"
            class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-5 pt-4 dark:border-slate-800">
                <h2 class="text-base font-semibold text-slate-800 dark:text-white">Hak Akses per Peran</h2>
                <p class="mb-1 mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                    Pilih peran, lalu tentukan level akses tiap modul:
                    <span class="font-medium text-slate-500 dark:text-slate-400">Lihat</span> (buka &amp; baca),
                    <span class="font-medium text-slate-500 dark:text-slate-400">Kelola</span> (tambah/ubah), atau
                    <span class="font-medium text-red-500 dark:text-red-400">Penuh</span> (termasuk hapus). Level yang lebih
                    tinggi otomatis mencakup yang di bawahnya.
                </p>
                <p class="mb-4 text-xs text-slate-400 dark:text-slate-500">
                    Super Admin selalu punya akses penuh ke semua modul, jadi tidak ditampilkan di sini. Perubahan tersimpan
                    otomatis.
                </p>

                <div class="flex gap-1 overflow-x-auto">
                    @foreach ($editableRoles as $role)
                        <button type="button" @click="roleId = {{ $role->id }}"
                            :class="roleId === {{ $role->id }} ?
                                'border-blue-600 text-blue-700 dark:border-blue-400 dark:text-blue-400' :
                                'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            class="shrink-0 whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold transition">
                            {{ $role->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 gap-x-8 gap-y-6 p-5 lg:grid-cols-2">
                <template x-for="group in groups" :key="group.name">
                    <div>
                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600"
                            x-text="group.name"></p>
                        <div class="space-y-1">
                            <template x-for="feature in group.features" :key="feature.key">
                                <div
                                    class="flex items-center justify-between gap-3 rounded-lg px-2 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <span class="truncate text-sm text-slate-600 dark:text-slate-300"
                                        x-text="feature.label"></span>
                                    <select :value="level(feature)" @change="setLevel(feature, $event.target.value)"
                                        :disabled="busy"
                                        :class="level(feature) === 'delete' ? 'text-red-600 dark:text-red-400' : ''"
                                        class="w-40 shrink-0 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm text-slate-800 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                        <option value="none">Tidak ada</option>
                                        <template x-for="tier in feature.tiers" :key="tier">
                                            <option :value="tier" :selected="level(feature) === tier"
                                                x-text="tierLabel(feature, tier)"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ============ BAGIAN 2: HAK AKSES EKSTRA PER DIVISI ============ --}}
        @if ($divisionFeatures->isNotEmpty() && $divisions->isNotEmpty())
            <div x-data="divisionAccess()"
                class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white">Hak Akses per Divisi</h2>
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                        Pilih divisi, lalu tentukan untuk tiap fitur: <span
                            class="font-medium text-slate-500 dark:text-slate-400">seberapa jauh</span> aksesnya
                        dan <span class="font-medium text-slate-500 dark:text-slate-400">siapa di divisi itu</span> yang
                        boleh (Kadiv, Sekdiv, dan/atau Anggota). Perubahan tersimpan otomatis.
                    </p>
                </div>

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <span class="font-medium">Divisi</span>
                        <select x-model.number="divisionId"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }} ({{ $division->abbreviation }})
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <select x-model.number="copyFrom"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="">Salin dari divisi…</option>
                            <template x-for="d in otherDivisions" :key="d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                        <button type="button" @click="copyAccess()" :disabled="!copyFrom || busy"
                            class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-40 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                            Salin
                        </button>
                    </div>
                </div>

                <div
                    class="hidden border-b border-slate-100 bg-slate-50/70 px-5 py-2.5 text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-500 sm:grid sm:grid-cols-12 sm:gap-4">
                    <div class="col-span-4">Fitur</div>
                    <div class="col-span-3">Level akses</div>
                    <div class="col-span-5">Siapa yang boleh</div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    <template x-for="feature in features" :key="feature.key">
                        <div class="grid grid-cols-1 items-center gap-3 px-5 py-3.5 sm:grid-cols-12 sm:gap-4">
                            <div class="sm:col-span-4">
                                <div class="text-sm font-semibold text-slate-800 dark:text-white" x-text="feature.label">
                                </div>
                                <div class="text-xs text-slate-400 dark:text-slate-500" x-text="feature.group"></div>
                            </div>

                            <div class="sm:col-span-3">
                                <select :value="current(feature).level" @change="setLevel(feature, $event.target.value)"
                                    :disabled="busy"
                                    :class="current(feature).level === 'delete' ? 'text-red-600 dark:text-red-400' : ''"
                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                    <option value="none">Tidak ada</option>
                                    <template x-for="tier in feature.tiers" :key="tier">
                                        <option :value="tier" :selected="current(feature).level === tier"
                                            x-text="tierLabel(feature, tier)"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 sm:col-span-5"
                                :class="current(feature).level === 'none' ? 'opacity-40' : ''">
                                <template x-for="who in [['kadiv', 'Kadiv'], ['sekdiv', 'Sekdiv'], ['anggota', 'Anggota']]"
                                    :key="who[0]">
                                    <label
                                        class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                        <input type="checkbox" :checked="current(feature)[who[0]]"
                                            :disabled="current(feature).level === 'none' || busy"
                                            @change="setScope(feature, who[0], $event.target.checked)"
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                        <span x-text="who[1]"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @endif

        {{-- ============ BAGIAN 3: MAPPING AKUN PENGURUS ============ --}}
        <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            x-data="{ showAddAccount: {{ $errors->hasAny(['member_id', 'email', 'role_id']) ? 'true' : 'false' }} }">
            <div
                class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white">Mapping Akun Pengurus</h2>
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Hubungkan akun login dengan data pengurus
                        &amp; tentukan perannya.</p>
                </div>
                @if (auth()->user()->isSuperAdmin())
                    <button type="button" @click="showAddAccount = !showAddAccount"
                        class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span x-text="showAddAccount ? 'Tutup Form' : 'Buatkan Akun Login'"></span>
                    </button>
                @endif
            </div>

            @if (auth()->user()->isSuperAdmin())
                <div x-show="showAddAccount" x-cloak x-transition
                    class="border-b border-slate-100 bg-slate-50/70 px-5 py-5 dark:border-slate-800 dark:bg-slate-800/30">
                    @if ($membersWithoutAccount->isEmpty())
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Semua pengurus aktif sudah memiliki akun login. Tambahkan data pengurus baru dulu di menu
                            <a href="{{ route('members.index') }}"
                                class="font-semibold text-blue-600 hover:underline dark:text-blue-400">Data Pengurus</a>
                            sebelum membuatkan akun untuknya.
                        </p>
                    @else
                        <form action="{{ route('roles-management.users.store') }}" method="POST"
                            class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
                            @csrf
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pengurus</label>
                                <x-dropdown-select name="member_id" placeholder="Pilih pengurus..." :required="true"
                                    :selected="old('member_id')" :options="$membersWithoutAccount->mapWithKeys(
                                        fn($member) => [
                                            $member->id =>
                                                $member->name .
                                                ($member->division ? ' - ' . $member->division->name : ''),
                                        ],
                                    )" />
                                @error('member_id')
                                    <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Email
                                    Login</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    placeholder="nama@email.com"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <label
                                        class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Peran</label>
                                    <x-dropdown-select name="role_id" placeholder="Pilih peran..." :required="true"
                                        :selected="old('role_id')" :options="$assignableRoles->pluck('name', 'id')" />
                                    @error('role_id')
                                        <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="mb-[1px] shrink-0 rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                                    Buat
                                </button>
                            </div>
                        </form>
                        <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">
                            Sandi awal akan dibuat otomatis dan ditampilkan sekali setelah akun berhasil dibuat -
                            segera sampaikan langsung ke pengurus yang bersangkutan.
                        </p>
                    @endif
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Nama Akun</th>
                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Email Login</th>
                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Pengurus / Divisi</th>
                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Peran</th>
                            @if (auth()->user()->isSuperAdmin())
                                <th
                                    class="whitespace-nowrap px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($users as $user)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">
                                    {{ $user->name }}
                                    @if ($user->id === auth()->id())
                                        <span
                                            class="ml-1.5 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-500 dark:bg-slate-800 dark:text-slate-400">Anda</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if ($user->id === auth()->id() || !auth()->user()->isSuperAdmin())
                                        <span class="text-sm text-slate-500 dark:text-slate-400"
                                            title="{{ $user->id === auth()->id() ? 'Ubah email Anda sendiri lewat Pengaturan Akun' : 'Hanya Super Admin yang dapat mengubah email login pengguna lain' }}">
                                            {{ $user->email }}
                                        </span>
                                    @else
                                        <input type="email" value="{{ $user->email }}"
                                            @change="updateUserField({{ $user->id }}, 'email', $event.target.value)"
                                            class="w-full min-w-[200px] rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-700 focus:border-blue-400 focus:ring-blue-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <x-dropdown-select placeholder="- Belum dipetakan -" class="min-w-[180px]"
                                        :selected="$user->member_id" :disabled="$user->id === auth()->id()
                                            ? 'Tidak dapat mengubah data akun sendiri di sini'
                                            : false"
                                        on-select="updateUserField({{ $user->id }}, 'member_id', value)"
                                        :options="$members->mapWithKeys(
                                            fn($member) => [
                                                $member->id =>
                                                    $member->name .
                                                    ($member->division
                                                        ? ' (' . $member->division->abbreviation . ')'
                                                        : ''),
                                            ],
                                        )" />
                                </td>
                                <td class="px-5 py-4">
                                    <x-dropdown-select placeholder="- Belum ada peran -" class="min-w-[160px]"
                                        :selected="$user->role_id" :disabled="$user->id === auth()->id()
                                            ? 'Tidak dapat mengubah peran akun sendiri di sini'
                                            : false"
                                        on-select="updateUserField({{ $user->id }}, 'role_id', value)"
                                        :options="$roles->pluck('name', 'id')" />
                                </td>
                                @if (auth()->user()->isSuperAdmin())
                                    <td class="px-5 py-4 text-center">
                                        @if ($user->id !== auth()->id())
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" title="Reset sandi akun ini ke sandi baru"
                                                    @click="if (confirm('Reset sandi untuk {{ $user->email }}? Sandi lama akan langsung tidak berlaku.')) resetPassword({{ $user->id }})"
                                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                                    Reset Sandi
                                                </button>
                                                <button type="button" title="Hapus akun login ini"
                                                    @click="if (confirm('Hapus akun login \'{{ $user->email }}\'? Data pengurus di Data Pengurus TIDAK ikut terhapus, hanya akses loginnya. Tindakan ini tidak dapat dibatalkan.')) deleteUser({{ $user->id }}, $el)"
                                                    class="rounded-lg border border-red-200 p-1.5 text-red-500 transition hover:bg-red-50 dark:border-red-800/60 dark:text-red-400 dark:hover:bg-red-500/10">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"
                                                        class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-300 dark:text-slate-600">-</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isSuperAdmin() ? 5 : 4 }}"
                                    class="px-5 py-16 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada
                                    akun.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function rolesManagement() {
            return {
                notice: '',
                noticeType: 'success',

                showNotice(message, type = 'success', persist = false) {
                    this.notice = message;
                    this.noticeType = type;
                    clearTimeout(this._noticeTimeout);
                    if (!persist) {
                        this._noticeTimeout = setTimeout(() => (this.notice = ''), 4000);
                    }
                },

                updateUserField(userId, field, value) {
                    fetch(`{{ url('peran-akses/pengguna') }}/${userId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                [field]: value || null
                            }),
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan perubahan.');
                            this.showNotice('Data akun berhasil diperbarui.');
                        })
                        .catch((err) => this.showNotice(err.message, 'error'));
                },

                resetPassword(userId) {
                    fetch(`{{ url('peran-akses/pengguna') }}/${userId}/atur-ulang-sandi`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal mereset sandi.');
                            // Sandi baru hanya ditampilkan sekali di sini - segera sampaikan ke pengguna secara langsung/pribadi.
                            this.showNotice(
                                `Sandi baru untuk ${data.email}: ${data.new_password} - segera sampaikan langsung ke pengguna.`,
                                'success', true);
                        })
                        .catch((err) => this.showNotice(err.message, 'error'));
                },

                deleteUser(userId, buttonEl) {
                    fetch(`{{ url('peran-akses/pengguna') }}/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menghapus akun.');
                            // Baris akun langsung dihapus dari tabel tanpa perlu reload halaman.
                            buttonEl.closest('tr')?.remove();
                            this.showNotice('Akun login berhasil dihapus.');
                        })
                        .catch((err) => this.showNotice(err.message, 'error'));
                },
            }
        }
        // Hak akses per peran: satu level per modul, tersimpan otomatis.
        function roleAccess() {
            return {
                features: @json($roleFeaturesUi),
                access: @json($roleAccess),
                roleId: {{ $editableRoles->first()->id ?? 'null' }},
                busy: false,

                get groups() {
                    const map = {};
                    this.features.forEach((f) => {
                        const name = f.group || 'Lainnya';
                        (map[name] = map[name] || {
                            name,
                            features: []
                        }).features.push(f);
                    });
                    return Object.values(map);
                },

                level(feature) {
                    return (this.access[this.roleId] || {})[feature.key] || 'none';
                },

                tierLabel(feature, tier) {
                    if (feature.tiers.length === 1) return 'Aktif';
                    if (tier === 'view') return 'Lihat';
                    if (tier === 'manage') return 'Kelola';
                    return feature.tiers.includes('manage') ? 'Penuh (+ hapus)' : 'Lihat + hapus';
                },

                setLevel(feature, level) {
                    const roleId = this.roleId;
                    const previous = this.level(feature);
                    this.access[roleId][feature.key] = level;
                    this.busy = true;

                    fetch(`{{ url('peran-akses/peran') }}/${roleId}/akses/${feature.key}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                level
                            }),
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan perubahan.');
                            this.access[roleId][feature.key] = data.level;
                            this.$dispatch('notice', {
                                message: 'Hak akses berhasil diperbarui.',
                                type: 'success'
                            });
                        })
                        .catch((err) => {
                            this.access[roleId][feature.key] = previous;
                            this.$dispatch('notice', {
                                message: err.message,
                                type: 'error'
                            });
                        })
                        .finally(() => (this.busy = false));
                },
            };
        }

        // Hak akses per divisi: satu level + siapa yang boleh, tersimpan otomatis.
        function divisionAccess() {
            const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
            const headers = () => ({
                'X-CSRF-TOKEN': csrf(),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            });

            return {
                features: @json($divisionFeaturesUi),
                access: @json($divisionAccess),
                divisions: @json($divisionsUi),
                divisionId: {{ $divisions->first()->id }},
                copyFrom: '',
                busy: false,

                get otherDivisions() {
                    return this.divisions.filter((d) => d.id !== this.divisionId);
                },

                notify(message, type = 'success') {
                    this.$dispatch('notice', {
                        message,
                        type
                    });
                },

                current(feature) {
                    return this.access[this.divisionId][feature.key];
                },

                tierLabel(feature, tier) {
                    if (feature.tiers.length === 1) return 'Aktif';
                    if (tier === 'view') return 'Lihat';
                    if (tier === 'manage') return 'Kelola';
                    return feature.tiers.includes('manage') ? 'Penuh (+ hapus)' : 'Lihat + hapus';
                },

                setLevel(feature, level) {
                    const state = {
                        ...this.current(feature),
                        level
                    };
                    // Pertama kali dinyalakan: default Kadiv & Sekdiv saja.
                    if (level !== 'none' && !state.kadiv && !state.sekdiv && !state.anggota) {
                        state.kadiv = true;
                        state.sekdiv = true;
                    }
                    this.save(feature, state);
                },

                setScope(feature, who, checked) {
                    this.save(feature, {
                        ...this.current(feature),
                        [who]: checked
                    });
                },

                save(feature, state) {
                    const divisionId = this.divisionId;
                    const previous = {
                        ...this.access[divisionId][feature.key]
                    };
                    this.access[divisionId][feature.key] = state;
                    this.busy = true;

                    fetch(`{{ url('peran-akses/divisi') }}/${divisionId}/akses/${feature.key}`, {
                            method: 'PUT',
                            headers: headers(),
                            body: JSON.stringify(state),
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan perubahan.');
                            this.access[divisionId][feature.key] = data;
                            this.notify('Hak akses divisi berhasil diperbarui.');
                        })
                        .catch((err) => {
                            this.access[divisionId][feature.key] = previous;
                            this.notify(err.message, 'error');
                        })
                        .finally(() => (this.busy = false));
                },

                copyAccess() {
                    if (!this.copyFrom) return;
                    if (!confirm('Pengaturan akses divisi ini akan ditimpa dengan milik divisi yang dipilih. Lanjutkan?'))
                        return;

                    const divisionId = this.divisionId;
                    this.busy = true;

                    fetch(`{{ url('peran-akses/divisi') }}/${divisionId}/salin-akses`, {
                            method: 'POST',
                            headers: headers(),
                            body: JSON.stringify({
                                from_division_id: this.copyFrom
                            }),
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menyalin hak akses.');
                            this.access[divisionId] = data.access;
                            this.copyFrom = '';
                            this.notify('Hak akses divisi berhasil disalin.');
                        })
                        .catch((err) => this.notify(err.message, 'error'))
                        .finally(() => (this.busy = false));
                },
            };
        }
    </script>
@endsection
