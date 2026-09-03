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
        // Mencatat setiap kunjungan halaman publik secara ringan, untuk
        // ditampilkan sebagai "Statistik Website" di Dashboard admin
        // (total kunjungan, pengunjung unik, tren harian, halaman terpopuler).
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('path', 255);
            $table->string('route_name', 100)->nullable();
            // Hash dari IP + User Agent + tanggal — dipakai untuk menghitung
            // "pengunjung unik per hari" tanpa menyimpan IP mentah pengunjung.
            $table->string('visitor_key', 64);
            $table->string('referrer', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('created_at');
            $table->index('path');
            $table->index('visitor_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
