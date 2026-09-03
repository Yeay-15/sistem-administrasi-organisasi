<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Division;
use App\Models\Member;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleManagementController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        $permissionMatrix = $this->buildPermissionMatrix(Permission::orderBy('group')->orderBy('label')->get());

        // Hak akses per-Divisi hanya menampilkan permission grup "Konten"
        // (mis. Berita, Galeri) — permission lain (mis. Kelola Agenda) memang
        // secara desain hanya diatur lewat peran (Role), bukan divisi.
        // Menambah permission baru ke grup ini otomatis muncul di sini tanpa
        // perlu ubah kode apa pun. Dipakai struktur baris yang sama (3 toggle:
        // Lihat/Kelola/Hapus) seperti matrix peran di atas.
        $divisionPermissionMatrix = $this->buildPermissionMatrix(
            Permission::where('group', 'Konten')->orderBy('label')->get()
        )->get('Konten', collect());
        $divisions = Division::with('permissions')->orderBy('name')->get();

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
            'permissionMatrix',
            'divisions',
            'divisionPermissionMatrix',
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
     * Menyalakan/mematikan satu permission untuk satu role (dari tombol toggle).
     */
    public function togglePermission(Request $request, Role $role, Permission $permission)
    {
        if ($role->isSuperAdmin()) {
            return response()->json([
                'message' => 'Super Admin selalu memiliki akses penuh dan tidak dapat diubah.',
            ], 422);
        }

        $isActive = $role->permissions()->where('permission_id', $permission->id)->exists();

        // Cegah admin yang sedang login menonaktifkan akses \"Manajemen Peran & Akses\"
        // untuk perannya sendiri, supaya tidak langsung terkunci dari halaman ini.
        if ($isActive && $permission->slug === 'manage_roles' && $role->id === Auth::user()->role_id) {
            return response()->json([
                'message' => 'Anda tidak dapat menonaktifkan akses ini untuk peran Anda sendiri.',
            ], 422);
        }

        if ($isActive) {
            $role->permissions()->detach($permission->id);
        } else {
            $role->permissions()->attach($permission->id);
        }

        AuditLog::record(
            'Ubah Hak Akses',
            sprintf(
                '%s hak akses "%s" untuk peran "%s".',
                $isActive ? 'Menonaktifkan' : 'Mengaktifkan',
                $permission->label,
                $role->name
            )
        );

        return response()->json(['active' => ! $isActive]);
    }

    /**
     * Menyalakan/mematikan satu permission untuk satu Divisi (hak akses
     * ekstra per-divisi, terlepas dari peran anggotanya).
     */
    public function toggleDivisionPermission(Request $request, Division $division, Permission $permission)
    {
        $isActive = $division->permissions()->where('permission_id', $permission->id)->exists();

        if ($isActive) {
            $division->permissions()->detach($permission->id);
        } else {
            $division->permissions()->attach($permission->id);
        }

        AuditLog::record(
            'Ubah Hak Akses Divisi',
            sprintf(
                '%s hak akses "%s" untuk Divisi "%s".',
                $isActive ? 'Menonaktifkan' : 'Mengaktifkan',
                $permission->label,
                $division->name
            )
        );

        return response()->json(['active' => ! $isActive]);
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
