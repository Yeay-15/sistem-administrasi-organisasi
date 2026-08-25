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
        Schema::table('home_settings', function (Blueprint $table) {
            // Mengatur siapa yang boleh mengirim formulir Aspirasi di Portal Publik:
            // - public          : terbuka untuk siapa saja (termasuk anonim)
            // - pengurus_only   : hanya pengunjung yang sedang login (auth) yang bisa mengirim
            // - nonaktif        : formulir disembunyikan, hanya tampil info kontak & medsos
            $table->string('aspiration_mode')->default('public')->after('chairman_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn('aspiration_mode');
        });
    }
};
