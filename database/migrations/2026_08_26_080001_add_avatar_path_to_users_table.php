<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Avatar akun (ranah privat pengguna) — TERPISAH dari 'photo_path'
            // di tabel members (foto resmi/PDH pengurus yang dikelola Sekretariat
            // dan tampil di portal publik). Keduanya sengaja tidak digabung.
            $table->string('avatar_path')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });
    }
};
