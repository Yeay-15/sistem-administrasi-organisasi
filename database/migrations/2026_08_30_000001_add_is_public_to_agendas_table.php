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
        // Menentukan apakah sebuah agenda ditampilkan ke halaman publik
        // (mis. kalender Portal Publik) atau hanya terlihat oleh pengurus di
        // dashboard admin. Default true supaya agenda lama yang sudah ada
        // tetap tampil ke publik seperti sebelumnya (tidak ada yang
        // tiba-tiba hilang setelah migration ini dijalankan).
        Schema::table('agendas', function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
