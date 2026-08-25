@extends('layouts.app')

@section('title', 'Manajemen Peran & Akses - KATIBER')

@section('content')
    <div x-data="rolesManagement()" class="space-y-8">

        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen Peran & Akses</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Atur hak akses tiap peran, hak akses ekstra per divisi, lalu petakan akun login ke peran &amp; data pengurus yang sesuai.
            </p>
        </div>

        {{-- Notifikasi hasil aksi (toggle / update role) --}}
        <div x-show="notice" x-cloak x-transition.opacity
            :class="noticeType === 'error'
                ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-800/60 dark:bg-red-500/10 dark:text-red-400'
                : 'border-green-200 bg-green-50 text-green-700 dark:border-green-800/60 dark:bg-green-500/10 dark:text-green-400'"
            class="theme-transition flex items-start gap-3 rounded-xl border px-4 py-3 text-sm">
            <span x-text="notice"></span>
        </div>

        {{-- ============ BAGIAN 1: MATRIX HAK AKSES PER PERAN ============ --}}
        <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-5 pt-4 dark:border-slate-800">
                <h2 class="text-base font-semibold text-slate-800 dark:text-white">Matrix Hak Akses per Peran</h2>
                <p class="mb-1 mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                    Setiap modul punya dua kolom: <span class="font-medium text-slate-500 dark:text-slate-400">Lihat</span> (buka & baca halaman) dan
                    <span class="font-medium text-slate-500 dark:text-slate-400">Kelola</span> (tambah/edit/hapus). Kelola otomatis mencakup Lihat.
                </p>
                <p class="mb-4 text-xs text-slate-400 dark:text-slate-500">Perubahan tersimpan otomatis.</p>

                {{-- Tabs --}}
                <div class="flex gap-1 overflow-x-auto">
                    @foreach ($roles as $role)
                        <button type="button" @click="activeRole = {{ $role->id }}"
                            :class="activeRole === {{ $role->id }}
                                ? 'border-blue-600 text-blue-700 dark:border-blue-400 dark:text-blue-400'
                                : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                            class="shrink-0 whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold transition">
                            {{ $role->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="p-5">
                @foreach ($roles as $role)
                    <div x-show="activeRole === {{ $role->id }}" x-cloak>
                        @if ($role->isSuperAdmin())
                            <div class="mb-4 flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-3 text-sm text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Super Admin selalu memiliki akses penuh ke seluruh modul dan tidak dapat dibatasi.</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($permissionMatrix as $group => $rows)
                                <div>
                                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">
                                        {{ $group ?: 'Lainnya' }}
                                    </p>
                                    <div class="space-y-1">
                                        @foreach ($rows as $row)
                                            <div class="flex items-center justify-between gap-3 rounded-lg px-2 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                                <span class="truncate text-sm text-slate-600 dark:text-slate-300">{{ $row['label'] }}</span>

                                                <div class="flex shrink-0 items-center gap-3">
                                                    @foreach ([['perm' => $row['view'], 'label' => 'Lihat'], ['perm' => $row['manage'], 'label' => 'Kelola']] as $col)
                                                        @if ($col['perm'])
                                                            @php
                                                                $permission = $col['perm'];
                                                                $isActive = $role->isSuperAdmin() || $role->permissions->contains('id', $permission->id);
                                                            @endphp
                                                            <div class="flex flex-col items-center gap-1"
                                                                x-data="{ active: {{ $isActive ? 'true' : 'false' }}, loading: false }">
                                                                <span class="text-[10px] font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $col['label'] }}</span>

                                                                @if ($role->isSuperAdmin())
                                                                    <span class="inline-flex h-5 w-9 shrink-0 items-center rounded-full bg-blue-600 px-0.5">
                                                                        <span class="h-4 w-4 translate-x-4 rounded-full bg-white shadow"></span>
                                                                    </span>
                                                                @else
                                                                    <button type="button" title="Toggle akses {{ $col['label'] }}"
                                                                        @click="loading = true; togglePermission({{ $role->id }}, {{ $permission->id }}, $data, () => loading = false)"
                                                                        :disabled="loading"
                                                                        :class="active ? 'bg-blue-600' : 'bg-slate-200 dark:bg-slate-700'"
                                                                        class="inline-flex h-5 w-9 shrink-0 items-center rounded-full px-0.5 transition disabled:opacity-50">
                                                                        <span :class="active ? 'translate-x-4' : 'translate-x-0'"
                                                                            class="h-4 w-4 rounded-full bg-white shadow transition-transform"></span>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ============ BAGIAN 2: HAK AKSES EKSTRA PER DIVISI ============ --}}
        @if ($divisionPermissions->isNotEmpty())
            <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-white">Hak Akses Ekstra per Divisi</h2>
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                        Berikan hak akses tambahan langsung ke sebuah Divisi, terlepas dari peran (Role) anggotanya — mis. Divisi Infokom boleh mengelola Berita &amp; Galeri tanpa perlu peran khusus. Nyalakan toggle di bawah untuk divisi lain kapan pun tanpa perlu mengubah kode.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                                <th class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Divisi</th>
                                @foreach ($divisionPermissions as $permission)
                                    <th class="whitespace-nowrap px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                        {{ $permission->label }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($divisions as $division)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <td class="px-5 py-3.5 text-sm font-semibold text-slate-800 dark:text-white">
                                        {{ $division->name }}
                                        <span class="ml-1 text-xs font-normal text-slate-400 dark:text-slate-500">({{ $division->abbreviation }})</span>
                                    </td>
                                    @foreach ($divisionPermissions as $permission)
                                        @php
                                            $isActive = $division->permissions->contains('id', $permission->id);
                                        @endphp
                                        <td class="px-5 py-3.5 text-center">
                                            <div class="inline-flex" x-data="{ active: {{ $isActive ? 'true' : 'false' }}, loading: false }">
                                                <button type="button" title="Toggle {{ $permission->label }} untuk {{ $division->name }}"
                                                    @click="loading = true; toggleDivisionPermission({{ $division->id }}, {{ $permission->id }}, $data, () => loading = false)"
                                                    :disabled="loading"
                                                    :class="active ? 'bg-blue-600' : 'bg-slate-200 dark:bg-slate-700'"
                                                    class="inline-flex h-6 w-11 shrink-0 items-center rounded-full px-0.5 transition disabled:opacity-50">
                                                    <span :class="active ? 'translate-x-5' : 'translate-x-0'"
                                                        class="h-5 w-5 rounded-full bg-white shadow transition-transform"></span>
                                                </button>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $divisionPermissions->count() + 1 }}" class="px-5 py-12 text-center text-sm text-slate-400 dark:text-slate-500">
                                        Belum ada data divisi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- ============ BAGIAN 3: MAPPING AKUN PENGURUS ============ --}}
        <div class="theme-transition overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <h2 class="text-base font-semibold text-slate-800 dark:text-white">Mapping Akun Pengurus</h2>
                <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Hubungkan akun login dengan data pengurus &amp; tentukan perannya.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 dark:border-slate-800 dark:bg-slate-800/40">
                            <th class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Akun</th>
                            <th class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Email Login</th>
                            <th class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pengurus / Divisi</th>
                            <th class="whitespace-nowrap px-5 py-3.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Peran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($users as $user)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">
                                    {{ $user->name }}
                                    @if ($user->id === auth()->id())
                                        <span class="ml-1.5 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-500 dark:bg-slate-800 dark:text-slate-400">Anda</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="px-5 py-4">
                                    <select @if($user->id === auth()->id()) disabled title="Tidak dapat mengubah data akun sendiri di sini"
                                        @else @change="updateUserField({{ $user->id }}, 'member_id', $event.target.value)" @endif
                                        class="w-full min-w-[180px] rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        <option value="">— Belum dipetakan —</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}" @selected($user->member_id === $member->id)>
                                                {{ $member->name }} @if($member->division) ({{ $member->division->abbreviation }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-5 py-4">
                                    <select @if($user->id === auth()->id()) disabled title="Tidak dapat mengubah peran akun sendiri di sini"
                                        @else @change="updateUserField({{ $user->id }}, 'role_id', $event.target.value)" @endif
                                        class="w-full min-w-[160px] rounded-lg border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        <option value="">— Belum ada peran —</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-16 text-center text-sm text-slate-400 dark:text-slate-500">Belum ada akun.</td>
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
                activeRole: {{ $roles->first()->id ?? 'null' }},
                notice: '',
                noticeType: 'success',

                showNotice(message, type = 'success') {
                    this.notice = message;
                    this.noticeType = type;
                    clearTimeout(this._noticeTimeout);
                    this._noticeTimeout = setTimeout(() => (this.notice = ''), 4000);
                },

                togglePermission(roleId, permissionId, itemData, done) {
                    fetch(`{{ url('roles-management/permissions') }}/${roleId}/${permissionId}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan perubahan.');
                            itemData.active = data.active;
                            this.showNotice('Hak akses berhasil diperbarui.');
                        })
                        .catch((err) => this.showNotice(err.message, 'error'))
                        .finally(() => done());
                },

                toggleDivisionPermission(divisionId, permissionId, itemData, done) {
                    fetch(`{{ url('roles-management/division-permissions') }}/${divisionId}/${permissionId}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan perubahan.');
                            itemData.active = data.active;
                            this.showNotice('Hak akses divisi berhasil diperbarui.');
                        })
                        .catch((err) => this.showNotice(err.message, 'error'))
                        .finally(() => done());
                },

                updateUserField(userId, field, value) {
                    fetch(`{{ url('roles-management/users') }}/${userId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ [field]: value || null }),
                        })
                        .then(async (res) => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan perubahan.');
                            this.showNotice('Data akun berhasil diperbarui.');
                        })
                        .catch((err) => this.showNotice(err.message, 'error'));
                },
            }
        }
    </script>
@endsection
