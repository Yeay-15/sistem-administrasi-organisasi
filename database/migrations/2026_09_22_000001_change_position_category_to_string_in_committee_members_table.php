<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Semula position_category adalah enum dengan pilihan tetap. Sekarang
     * dilonggarkan jadi string biasa supaya form bisa menyediakan opsi
     * "Lainnya" yang membolehkan pengguna mengetik peran sendiri (mis.
     * "Penanggung Jawab Lapangan") tanpa dibatasi daftar enum. Nilai yang
     * sudah tersimpan tidak berubah karena enum di MySQL memang disimpan
     * sebagai string juga.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE committee_members MODIFY position_category VARCHAR(100) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nilai custom (di luar enum lama) akan dipetakan ke 'Anggota Bidang'
        // dulu supaya rollback ke enum tidak gagal karena data tidak valid.
        DB::table('committee_members')
            ->whereNotIn('position_category', [
                'Ketua Panitia', 'Sekretaris Panitia', 'Bendahara Panitia',
                'Bendahara Peserta', 'Ketua Bidang', 'Anggota Bidang',
            ])
            ->update(['position_category' => 'Anggota Bidang']);

        DB::statement("ALTER TABLE committee_members MODIFY position_category ENUM('Ketua Panitia','Sekretaris Panitia','Bendahara Panitia','Bendahara Peserta','Ketua Bidang','Anggota Bidang') NOT NULL");
    }
};
