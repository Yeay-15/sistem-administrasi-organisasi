<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom `attendance_scope` ke tabel agendas.
     *
     * 'all'      -> agenda berlaku untuk seluruh pengurus aktif (perilaku lama/default).
     * 'division' -> agenda hanya berlaku untuk divisi tertentu (lihat tabel
     *               pivot agenda_division di migration berikutnya). Dipakai
     *               agar daftar absensi & statistik kehadiran per pengurus
     *               tidak "menghukum" anggota divisi lain yang memang tidak
     *               diundang ke agenda tersebut.
     */
    public function up(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->string('attendance_scope', 20)->default('all')->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn('attendance_scope');
        });
    }
};
