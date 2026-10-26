<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fitur "Event Unggulan" sudah dihapus dari sistem, tapi izin akses
     * (view/manage/delete_events) masih tersisa di tabel permissions sehingga
     * tetap muncul di halaman Manajemen Peran & Akses. Bersihkan di sini.
     */
    public function up(): void
    {
        $ids = DB::table('permissions')
            ->whereIn('slug', ['view_events', 'manage_events', 'delete_events'])
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('division_permission')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }

    public function down(): void
    {
        // Tidak dikembalikan — fitur Event Unggulan sudah tidak ada.
    }
};
