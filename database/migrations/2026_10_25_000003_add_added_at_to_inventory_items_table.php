<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tanggal barang ditambahkan ke inventaris.
     *
     * Barang yang SUDAH ada saat migrasi ini dijalankan (data awal dari
     * dokumen inventaris) diisi dengan tanggal pendataan di dokumen tsb,
     * yaitu 10 Oktober 2026. Barang baru lewat form otomatis terisi hari ini
     * (bisa diubah manual).
     */
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->date('added_at')->nullable()->after('location');
        });

        DB::table('inventory_items')->whereNull('added_at')->update(['added_at' => '2026-10-10']);
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('added_at');
        });
    }
};
