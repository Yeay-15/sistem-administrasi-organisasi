<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Toggle apakah timeline "Estafet Kepemimpinan" tampil di halaman publik
     * (Tentang Kami). Default true supaya tampilan publik yang sudah ada
     * tidak berubah sebelum admin mematikannya.
     */
    public function up(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->boolean('show_leaders_public')->default(true)->after('aspiration_mode');
        });
    }

    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn('show_leaders_public');
        });
    }
};
