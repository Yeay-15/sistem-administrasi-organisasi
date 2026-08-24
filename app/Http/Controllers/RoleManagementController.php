<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
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
        $permissions = Permission::orderBy('group')->orderBy('label')->get()->groupBy('group');
        $users = User::with(['role', 'member.division'])->orderBy('name')->get();
        $members = Member::where('status', 'Aktif')->orderBy('name')->get();

        return view('roles_management.index', compact('roles', 'permissions', 'users', 'members'));
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

        // Cegah admin yang sedang login menonaktifkan akses "Manajemen Peran & Akses"
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
