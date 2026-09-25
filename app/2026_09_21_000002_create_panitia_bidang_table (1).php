<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menjadikan tabel `members` sebagai basis data "orang" secara umum,
     * bukan cuma pengurus: menambah kolom `membership_type` untuk membedakan
     * Pengurus vs Non-Pengurus (mis. anggota kepanitiaan dari luar struktur
     * organisasi), dan melonggarkan kolom yang memang khusus berlaku untuk
     * pengurus (divisi, jabatan, angkatan, NIM, tanggal bergabung) supaya
     * boleh kosong untuk Non-Pengurus. MemberController tetap mewajibkan
     * kolom-kolom ini di level validasi untuk alur Pengurus seperti biasa.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Default 'Pengurus' supaya seluruh data pengurus yang sudah ada
            // otomatis tetap tampil sebagai pengurus tanpa perlu migrasi data manual.
            $table->string('membership_type')->default('Pengurus')->after('status');
        });

        // Pakai statement SQL mentah (bukan ->change()) supaya migration ini
        // tidak butuh paket doctrine/dbal yang belum tentu terpasang.
        DB::statement('ALTER TABLE members MODIFY division_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE members MODIFY position VARCHAR(255) NULL');
        DB::statement('ALTER TABLE members MODIFY batch VARCHAR(255) NULL');
        DB::statement('ALTER TABLE members MODIFY student_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE members MODIFY join_date DATE NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sebelum mengembalikan kolom jadi NOT NULL, isi dulu baris yang kosong
        // supaya rollback tidak gagal karena melanggar constraint.
        DB::table('members')->whereNull('division_id')->update(['division_id' => DB::raw('(SELECT id FROM (SELECT id FROM divisions ORDER BY id ASC LIMIT 1) AS d)')]);
        DB::table('members')->whereNull('position')->update(['position' => 'Anggota Divisi']);
        DB::table('members')->whereNull('batch')->update(['batch' => '-']);
        DB::table('members')->whereNull('student_id')->update(['student_id' => '-']);
        DB::table('members')->whereNull('join_date')->update(['join_date' => now()]);

        DB::statement('ALTER TABLE members MODIFY division_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE members MODIFY position VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE members MODIFY batch VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE members MODIFY student_id VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE members MODIFY join_date DATE NOT NULL');

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('membership_type');
        });
    }
};
