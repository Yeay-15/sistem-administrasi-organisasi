<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan "siapa di divisi yang boleh" ke hak akses per-divisi:
     * Kadiv (Ketua Divisi), Sekdiv (Sekretaris Divisi), dan/atau Anggota.
     *
     * Default TRUE untuk ketiganya, jadi semua baris lama tetap berlaku
     * untuk seluruh anggota divisi - perilaku sebelum migration ini TIDAK
     * berubah sampai admin sendiri mempersempitnya lewat halaman Peran & Akses.
     */
    public function up(): void
    {
        Schema::table('division_permission', function (Blueprint $table) {
            $table->boolean('scope_kadiv')->default(true)->after('permission_id');
            $table->boolean('scope_sekdiv')->default(true)->after('scope_kadiv');
            $table->boolean('scope_anggota')->default(true)->after('scope_sekdiv');
        });
    }

    public function down(): void
    {
        Schema::table('division_permission', function (Blueprint $table) {
            $table->dropColumn(['scope_kadiv', 'scope_sekdiv', 'scope_anggota']);
        });
    }
};
