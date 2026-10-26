<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Division;
use App\Models\Member;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleManagementController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        $roleFeatures = $this->featuresFrom(Permission::orderBy('group')->orderBy('label')->get());
        $roleFeaturesUi = $roleFeatures
            ->map(fn (array $f) => Arr::only($f, ['key', 'label', 'group', 'tiers']))
            ->values();
        $roleAccess = $roles
            ->reject(fn (Role $role) => $role->isSuperAdmin())
            ->mapWithKeys(fn (Role $role) => [$role->id => $this->roleLevels($role, $roleFeatures)]);
        $editableRoles = $roles->reject(fn (Role $role) => $role->isSuperAdmin())->values();

        // Hak akses per-Divisi hanya menampilkan permission grup "Konten" dan
        // "Inventaris" (permission lain, mis. Kelola Agenda, memang hanya diatur
        // lewat peran). Setiap fitur = satu baris: satu level akses + siapa di
        // divisi yang boleh (Kadiv / Sekdiv / Anggota). Permission baru di grup
        // ini otomatis muncul tanpa ubah kode.
        $divisionFeatures = $this->featuresFrom(
            Permission::whereIn('group', ['Konten', 'Inventaris'])->orderBy('label')->get()
        );
        $divisions = Division::with('permissions')->orderBy('name')->get();

        $divisionFeaturesUi = $divisionFeatures
            ->map(fn (array $f) => Arr::only($f, ['key', 'label', 'group', 'tiers']))
            ->values();
        $divisionsUi = $divisions->map(fn (Division $d) => ['id' => $d->id, 'name' => $d->name])->values();

        $divisionAccess = $divisions->mapWithKeys(
            fn (Division $division) => [$division->id => $this->accessFor($division, $divisionFeatures)]
        );

        $users = User::with(['role', 'member.division'])->orderBy('name')->get();
        $members = Member::where('status', 'Aktif')->orderBy('name')->get();

        // Hanya pengurus aktif yang BELUM punya akun login — jadi dropdown
        // "Buatkan Akun Login" tidak akan pernah menampilkan orang yang
        // sudah punya akun (mencegah satu pengurus punya 2 akun sekaligus).
        $membersWithoutAccount = Member::where('status', 'Aktif')
            ->whereDoesntHave('user')
            ->orderBy('name')
            ->get();

        // Peran "Super Admin" sengaja tidak ditawarkan di form pembuatan
        // akun cepat ini — mencegah akun setingkat Super Admin dibuat tanpa
        // sengaja lewat jalur pintas.
        $assignableRoles = $roles->reject(fn ($role) => $role->isSuperAdmin());

        return view('roles_management.index', compact(
            'roles',
            'roleFeaturesUi',
            'roleAccess',
            'editableRoles',
            'divisions',
            'divisionFeatures',
            'divisionFeaturesUi',
            'divisionsUi',
            'divisionAccess',
            'users',
            'members',
            'membersWithoutAccount',
            'assignableRoles'
        ));
    }

    /**
     * Susun daftar permission menjadi baris-baris matrix: satu baris untuk
     * trio 'view_xxx' + 'manage_xxx' + 'delete_xxx' (ditampilkan sebagai
     * hingga tiga toggle — "Lihat", "Kelola", "Hapus"), atau baris dengan
     * lebih sedikit toggle untuk permission yang tidak lengkap trio-nya
     * (mis. 'manage_roles' yang cuma berdiri sendiri).
     *
     * Hasilnya dikelompokkan per 'group' (Pengurus, Kegiatan, dst) supaya
     * tampilannya tetap konsisten.
     */
    private function buildPermissionMatrix($permissions)
    {
        $rows = [];

        foreach ($permissions as $permission) {
            $base = preg_replace('/^(view_|manage_|delete_)/', '', $permission->slug);
            $key = $permission->group . '|' . $base;

            if (! isset($rows[$key])) {
                $rows[$key] = [
                    'key' => $base,
                    'group' => $permission->group,
                    'label' => preg_replace('/^(Lihat|Kelola|Hapus)\s+/', '', $permission->label),
                    'view' => null,
                    'manage' => null,
                    'delete' => null,
                ];
            }

            if (str_starts_with($permission->slug, 'view_')) {
                $rows[$key]['view'] = $permission;
            } elseif (str_starts_with($permission->slug, 'delete_')) {
                $rows[$key]['delete'] = $permission;
            } else {
                $rows[$key]['manage'] = $permission;
            }
        }

        return collect($rows)->values()->groupBy('group');
    }

    /**
     * Level akses satu peran untuk semua fitur: tingkat tertinggi yang aktif.
     */
    private function roleLevels(Role $role, $features): array
    {
        $ownedIds = $role->permissions->pluck('id');
        $levels = [];

        foreach ($features as $feature) {
            $level = 'none';
            foreach ($feature['tiers'] as $tier) {
                if ($ownedIds->contains($feature['permissions'][$tier]->id)) {
                    $level = $tier;
                }
            }
            $levels[$feature['key']] = $level;
        }

        return $levels;
    }

    /**
     * Mengatur level akses satu peran pada satu fitur (none / view / manage / delete).
     * Level bertingkat: memilih level tinggi otomatis menyalakan level di bawahnya.
     */
    public function updateRoleAccess(Request $request, Role $role, string $feature)
    {
        if ($role->isSuperAdmin()) {
            return response()->json([
                'message' => 'Super Admin selalu memiliki akses penuh dan tidak dapat diubah.',
            ], 422);
        }

        $row = $this->featuresFrom(Permission::orderBy('group')->orderBy('label')->get())
            ->firstWhere('key', $feature);
        abort_if(! $row, 404);

        $validated = $request->validate([
            'level' => ['required', Rule::in(array_merge(['none'], $row['tiers']))],
        ]);
        $level = $validated['level'];

        // Cegah admin mematikan akses "Manajemen Peran & Akses" untuk perannya
        // sendiri, supaya tidak langsung terkunci dari halaman ini.
        if ($feature === 'roles' && $level === 'none' && $role->id === Auth::user()->role_id) {
            return response()->json([
                'message' => 'Anda tidak dapat menonaktifkan akses ini untuk peran Anda sendiri.',
            ], 422);
        }

        $cutoff = $level === 'none' ? -1 : array_search($level, $row['tiers'], true);

        $attach = [];
        $detach = [];
        foreach ($row['tiers'] as $index => $tier) {
            $permissionId = $row['permissions'][$tier]->id;
            if ($index <= $cutoff) {
                $attach[] = $permissionId;
            } else {
                $detach[] = $permissionId;
            }
        }

        if ($detach) {
            $role->permissions()->detach($detach);
        }
        if ($attach) {
            $role->permissions()->syncWithoutDetaching($attach);
        }

        AuditLog::record(
            'Ubah Hak Akses',
            sprintf('Akses peran "%s" untuk "%s": %s.', $role->name, $row['label'], $level === 'none' ? 'Tidak ada' : $level)
        );

        return response()->json(['level' => $level]);
    }

    /**
     * Tingkatan akses berurutan: lihat < kelola < hapus. Memilih satu tingkat
     * otomatis menyalakan tingkat di bawahnya, jadi kombinasi tak masuk akal
     * (mis. Hapus nyala tapi Lihat mati) tidak mungkin terjadi.
     */
    private const TIERS = ['view', 'manage', 'delete'];

    /**
     * Daftar fitur yang bisa diatur per divisi. Tiap fitur membawa tingkat yang
     * benar-benar tersedia (mis. Pengaturan Beranda hanya punya 'manage',
     * Aspirasi Mahasiswa hanya 'view' & 'delete').
     *
     * @return \Illuminate\Support\Collection<int, array{key:string,label:string,group:string,tiers:array<int,string>,permissions:array<string,Permission>}>
     */
    private function divisionFeatures()
    {
        return $this->featuresFrom(
            Permission::whereIn('group', ['Konten', 'Inventaris'])->orderBy('label')->get()
        );
    }

    /**
     * Ubah daftar permission menjadi daftar fitur bertingkat (lihat/kelola/hapus).
     */
    private function featuresFrom($permissions)
    {
        return $this->buildPermissionMatrix($permissions)
            ->flatten(1)
            ->map(function (array $row) {
                $tiers = array_values(array_filter(self::TIERS, fn ($tier) => $row[$tier] !== null));

                return [
                    'key' => $row['key'],
                    'label' => $row['label'],
                    'group' => $row['group'],
                    'tiers' => $tiers,
                    'permissions' => collect($tiers)->mapWithKeys(fn ($tier) => [$tier => $row[$tier]])->all(),
                ];
            })
            ->filter(fn (array $feature) => $feature['tiers'] !== [])
            ->values();
    }

    /**
     * Keadaan akses satu divisi untuk semua fitur: level tertinggi yang aktif
     * + centang Kadiv/Sekdiv/Anggota (diambil dari tingkat tertinggi itu).
     */
    private function accessFor(Division $division, $features): array
    {
        $result = [];

        foreach ($features as $feature) {
            $level = 'none';
            $scopes = ['kadiv' => false, 'sekdiv' => false, 'anggota' => false];

            foreach ($feature['tiers'] as $tier) {
                $attached = $division->permissions->firstWhere('id', $feature['permissions'][$tier]->id);

                if ($attached) {
                    $level = $tier;
                    $scopes = [
                        'kadiv' => (bool) $attached->pivot->scope_kadiv,
                        'sekdiv' => (bool) $attached->pivot->scope_sekdiv,
                        'anggota' => (bool) $attached->pivot->scope_anggota,
                    ];
                }
            }

            $result[$feature['key']] = ['level' => $level] + $scopes;
        }

        return $result;
    }

    /**
     * Mengatur akses satu divisi pada satu fitur sekaligus: level (none / view /
     * manage / delete) + siapa di divisi yang boleh (Kadiv, Sekdiv, Anggota).
     */
    public function updateDivisionAccess(Request $request, Division $division, string $feature)
    {
        $row = $this->divisionFeatures()->firstWhere('key', $feature);
        abort_if(! $row, 404);

        $validated = $request->validate([
            'level' => ['required', Rule::in(array_merge(['none'], $row['tiers']))],
            'kadiv' => ['required', 'boolean'],
            'sekdiv' => ['required', 'boolean'],
            'anggota' => ['required', 'boolean'],
        ]);

        $level = $validated['level'];
        $scopes = [
            'scope_kadiv' => $request->boolean('kadiv'),
            'scope_sekdiv' => $request->boolean('sekdiv'),
            'scope_anggota' => $request->boolean('anggota'),
        ];

        if ($level !== 'none' && ! in_array(true, $scopes, true)) {
            return response()->json([
                'message' => 'Pilih minimal satu: Kadiv, Sekdiv, atau Anggota.',
            ], 422);
        }

        $cutoff = $level === 'none' ? -1 : array_search($level, $row['tiers'], true);

        $attach = [];
        $detach = [];
        foreach ($row['tiers'] as $index => $tier) {
            $permissionId = $row['permissions'][$tier]->id;

            if ($index <= $cutoff) {
                $attach[$permissionId] = $scopes;
            } else {
                $detach[] = $permissionId;
            }
        }

        if ($detach) {
            $division->permissions()->detach($detach);
        }
        if ($attach) {
            $division->permissions()->syncWithoutDetaching($attach);
        }

        AuditLog::record(
            'Ubah Hak Akses Divisi',
            sprintf(
                'Akses Divisi "%s" untuk "%s": %s%s.',
                $division->name,
                $row['label'],
                $level === 'none' ? 'Tidak ada' : $level,
                $level === 'none' ? '' : ' (' . implode(', ', array_keys(array_filter([
                    'Kadiv' => $scopes['scope_kadiv'],
                    'Sekdiv' => $scopes['scope_sekdiv'],
                    'Anggota' => $scopes['scope_anggota'],
                ]))) . ')'
            )
        );

        return response()->json([
            'level' => $level,
            'kadiv' => $scopes['scope_kadiv'],
            'sekdiv' => $scopes['scope_sekdiv'],
            'anggota' => $scopes['scope_anggota'],
        ]);
    }

    /**
     * Menyalin seluruh pengaturan akses (fitur Konten & Inventaris) dari
     * divisi lain — menimpa pengaturan divisi tujuan.
     */
    public function copyDivisionAccess(Request $request, Division $division)
    {
        $validated = $request->validate([
            'from_division_id' => ['required', 'exists:divisions,id'],
        ]);

        if ((int) $validated['from_division_id'] === $division->id) {
            return response()->json(['message' => 'Pilih divisi sumber yang berbeda.'], 422);
        }

        $source = Division::with('permissions')->findOrFail($validated['from_division_id']);
        $features = $this->divisionFeatures();

        $featurePermissionIds = $features
            ->flatMap(fn (array $feature) => collect($feature['permissions'])->pluck('id'))
            ->all();

        $attach = [];
        foreach ($source->permissions as $permission) {
            if (in_array($permission->id, $featurePermissionIds, true)) {
                $attach[$permission->id] = [
                    'scope_kadiv' => (bool) $permission->pivot->scope_kadiv,
                    'scope_sekdiv' => (bool) $permission->pivot->scope_sekdiv,
                    'scope_anggota' => (bool) $permission->pivot->scope_anggota,
                ];
            }
        }

        $division->permissions()->detach($featurePermissionIds);
        if ($attach) {
            $division->permissions()->attach($attach);
        }

        AuditLog::record(
            'Salin Hak Akses Divisi',
            sprintf('Menyalin hak akses Divisi "%s" ke Divisi "%s".', $source->name, $division->name)
        );

        return response()->json([
            'access' => $this->accessFor($division->load('permissions'), $features),
        ]);
    }

    /**
     * Membuat akun login baru untuk pengurus yang sudah tercatat di Data
     * Pengurus tapi belum bisa login. Sandi awal di-generate otomatis (sama
     * seperti "Reset Sandi") dan hanya ditampilkan SEKALI lewat flash
     * session setelah redirect — Super Admin bertanggung jawab
     * menyampaikannya langsung ke pengurus yang bersangkutan.
     */
    public function storeUser(Request $request)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat membuat akun login baru.');

        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id', Rule::unique('users', 'member_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role_id' => ['required', 'exists:roles,id'],
        ], [
            'member_id.unique' => 'Pengurus ini sudah memiliki akun login.',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        abort_if($role->isSuperAdmin(), 422, 'Tidak dapat membuat akun dengan peran Super Admin lewat menu ini.');

        $member = Member::findOrFail($validated['member_id']);

        $newPassword = Str::password(10, symbols: false);

        $user = User::create([
            'name' => $member->name,
            'email' => $validated['email'],
            'password' => Hash::make($newPassword),
            'role_id' => $role->id,
            'member_id' => $member->id,
        ]);

        AuditLog::record(
            'Buat Akun Login',
            sprintf('Membuat akun login baru untuk pengurus "%s" (%s) dengan peran "%s".', $member->name, $user->email, $role->name)
        );

        return redirect()->route('roles-management.index')
            ->with('success', 'Akun login berhasil dibuat.')
            ->with('generated_account', [
                'name' => $member->name,
                'email' => $user->email,
                'password' => $newPassword,
            ]);
    }

    /**
     * Update pemetaan role/member untuk satu akun (dari dropdown di tabel akun).
     * Hanya field yang benar-benar dikirim yang akan diperbarui.
     */
    public function updateUser(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak dapat mengubah data akun Anda sendiri di sini.',
            ], 422);
        }

        // Mengganti email login pengguna lain sengaja dibatasi ke Super Admin
        // saja — field lain (role_id/member_id) tetap terbuka untuk siapa
        // pun yang punya akses "manage_roles", seperti sebelumnya.
        if ($request->has('email') && ! Auth::user()->isSuperAdmin()) {
            return response()->json([
                'message' => 'Hanya Super Admin yang dapat mengubah email login pengguna lain.',
            ], 403);
        }

        $validated = $request->validate([
            'role_id' => ['sometimes', 'nullable', 'exists:roles,id'],
            'member_id' => ['sometimes', 'nullable', 'exists:members,id'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->fill($validated);
        $user->save();

        AuditLog::record(
            'Ubah Akses Akun',
            sprintf('Memperbarui pemetaan peran/pengurus untuk akun "%s".', $user->email)
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * Menghapus akun login (mis. akun uji coba). Data pengurus di tabel
     * `members` TIDAK ikut terhapus — hanya akses loginnya yang dicabut.
     * Kalau kelak pengurus itu perlu login lagi, tinggal buatkan akun baru
     * lewat "Buatkan Akun Login" seperti biasa.
     */
    public function destroyUser(Request $request, User $user)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat menghapus akun login.');

        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 422);
        }

        $email = $user->email;
        $user->delete();

        AuditLog::record(
            'Hapus Akun Login',
            sprintf('Menghapus akun login "%s".', $email)
        );

        return response()->json(['status' => 'ok']);
    }

    /**
     * "Reset by Admin" — Super Admin mengembalikan sandi akun pengguna ke
     * sandi acak baru (menghindari kerumitan konfigurasi email reset-password
     * di fase awal). Sandi baru ditampilkan SEKALI di respons ini supaya
     * Super Admin bisa menyampaikannya langsung ke pengguna.
     */
    public function resetPassword(Request $request, User $user)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mereset sandi pengguna lain.');

        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Gunakan halaman Pengaturan Akun untuk mengubah sandi Anda sendiri.',
            ], 422);
        }

        $newPassword = Str::password(10, symbols: false);
        $user->password = Hash::make($newPassword);
        $user->save();

        AuditLog::record(
            'Reset Sandi Pengguna',
            sprintf('Mereset sandi akun "%s" ke sandi baru.', $user->email)
        );

        return response()->json([
            'email' => $user->email,
            'new_password' => $newPassword,
        ]);
    }
}
