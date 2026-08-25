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

class RoleManagementController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        $permissionMatrix = $this->buildPermissionMatrix(Permission::orderBy('group')->orderBy('label')->get());

        // Hak akses per-Divisi hanya menampilkan permission grup "Konten"
        // (mis. Kelola Berita, Kelola Galeri) — permission lain (mis. Kelola
        // Agenda) memang secara desain hanya diatur lewat peran (Role), bukan
        // divisi. Menambah permission baru ke grup ini otomatis muncul di
        // sini tanpa perlu ubah kode apa pun.
        $divisionPermissions = Permission::where('group', 'Konten')->orderBy('label')->get();
        $divisions = Division::with('permissions')->orderBy('name')->get();

        $users = User::with(['role', 'member.division'])->orderBy('name')->get();
        $members = Member::where('status', 'Aktif')->orderBy('name')->get();

        return view('roles_management.index', compact(
            'roles',
            'permissionMatrix',
            'divisions',
            'divisionPermissions',
            'users',
            'members'
        ));
    }

    /**
     * Susun daftar permission menjadi baris-baris matrix: satu baris untuk
     * pasangan 'view_xxx' + 'manage_xxx' (ditampilkan sebagai dua toggle,
     * "Lihat" & "Kelola"), atau satu baris dengan satu toggle untuk
     * permission yang berdiri sendiri (mis. 'manage_roles').
     *
     * Hasilnya dikelompokkan per 'group' (Pengurus, Kegiatan, dst) supaya
     * tampilannya tetap sama seperti matrix sebelumnya.
     */
    private function buildPermissionMatrix($permissions)
    {
        $rows = [];

        foreach ($permissions as $permission) {
            $base = preg_replace('/^(view_|manage_)/', '', $permission->slug);
            $key = $permission->group . '|' . $base;

            if (! isset($rows[$key])) {
                $rows[$key] = [
                    'group' => $permission->group,
                    'label' => preg_replace('/^(Lihat|Kelola)\s+/', '', $permission->label),
                    'view' => null,
                    'manage' => null,
                ];
            }

            if (str_starts_with($permission->slug, 'view_')) {
                $rows[$key]['view'] = $permission;
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
     * Update pemetaan role/member untuk satu akun (dari dropdown di tabel akun).
     * Hanya field yang benar-benar dikirim yang akan diperbarui.
     */
    public function updateUser(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak dapat mengubah peran akun Anda sendiri di sini.',
            ], 422);
        }

        $validated = $request->validate([
            'role_id' => ['sometimes', 'nullable', 'exists:roles,id'],
            'member_id' => ['sometimes', 'nullable', 'exists:members,id'],
        ]);

        $user->fill($validated);
        $user->save();

        AuditLog::record(
            'Ubah Akses Akun',
            sprintf('Memperbarui pemetaan peran/pengurus untuk akun "%s".', $user->email)
        );

        return response()->json(['status' => 'ok']);
    }
}
