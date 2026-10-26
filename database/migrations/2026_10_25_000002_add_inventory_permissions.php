<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PERMISSIONS = [
        ['slug' => 'view_inventory_items', 'label' => 'Lihat Inventaris', 'group' => 'Inventaris'],
        ['slug' => 'manage_inventory_items', 'label' => 'Kelola Inventaris', 'group' => 'Inventaris'],
        ['slug' => 'delete_inventory_items', 'label' => 'Hapus Inventaris', 'group' => 'Inventaris'],
    ];

    /**
     * Migration DATA (bukan skema): mendaftarkan 3 permission inventaris ke
     * database yang SUDAH berjalan, supaya langsung muncul di halaman
     * Peran & Akses tanpa harus menjalankan ulang RolePermissionSeeder.
     * Aman dijalankan berulang (tidak menimpa pengaturan yang sudah ada).
     *
     * Default awal (sekali saja, sama seperti kasus Infokom -> Berita/Galeri):
     * - Super Admin: semua permission baru.
     * - Divisi Inventaris (singkatan "Invent"), bila ada: Lihat + Kelola + Hapus.
     * Setelah itu semuanya bisa diatur bebas lewat toggle di Peran & Akses.
     */
    public function up(): void
    {
        $now = now();

        foreach (self::PERMISSIONS as $permission) {
            $exists = DB::table('permissions')->where('slug', $permission['slug'])->exists();

            if ($exists) {
                DB::table('permissions')->where('slug', $permission['slug'])->update([
                    'label' => $permission['label'],
                    'group' => $permission['group'],
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('permissions')->insert($permission + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('slug', array_column(self::PERMISSIONS, 'slug'))
            ->pluck('id');

        $superAdminId = DB::table('roles')->where('slug', 'super-admin')->value('id');
        if ($superAdminId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $superAdminId,
                    'permission_id' => $permissionId,
                ]);
            }
        }

        $inventDivisionId = DB::table('divisions')->where('abbreviation', 'Invent')->value('id');
        if ($inventDivisionId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('division_permission')->insertOrIgnore([
                    'division_id' => $inventDivisionId,
                    'permission_id' => $permissionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $ids = DB::table('permissions')
            ->whereIn('slug', array_column(self::PERMISSIONS, 'slug'))
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('division_permission')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
};
