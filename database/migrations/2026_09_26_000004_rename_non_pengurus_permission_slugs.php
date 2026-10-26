<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration DATA (bukan skema): menyamakan slug permission lama
     * (view/manage/delete_non_pengurus) ke Inggris (..._non_officers).
     *
     * Sengaja UPDATE baris yang sudah ada, BUKAN hapus+buat baru — supaya
     * kalau di database ini sudah ada role yang di-assign salah satu
     * permission tsb (lewat tabel pivot role_permission), assignment itu
     * TETAP nempel ke baris yang sama (cuma slug & labelnya yang berubah)
     * dan tidak ada yang kehilangan akses setelah deploy.
     */
    private const RENAMES = [
        'view_non_pengurus' => 'view_non_officers',
        'manage_non_pengurus' => 'manage_non_officers',
        'delete_non_pengurus' => 'delete_non_officers',
    ];

    public function up(): void
    {
        foreach (self::RENAMES as $old => $new) {
            DB::table('permissions')->where('slug', $old)->update(['slug' => $new]);
        }
    }

    public function down(): void
    {
        foreach (self::RENAMES as $old => $new) {
            DB::table('permissions')->where('slug', $new)->update(['slug' => $old]);
        }
    }
};
