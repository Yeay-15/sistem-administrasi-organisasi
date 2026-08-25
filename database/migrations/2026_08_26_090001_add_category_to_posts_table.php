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
        Schema::table('posts', function (Blueprint $table) {
            // Membedakan konten "Artikel & Berita" (opini/berita umum) dengan
            // "Laporan Kegiatan" (dokumentasi kegiatan yang sudah berlangsung).
            // Kolom ini dipakai oleh Portal Publik untuk memfilter dua menu Media yang berbeda.
            $table->enum('category', ['Artikel & Berita', 'Laporan Kegiatan'])
                ->default('Artikel & Berita')
                ->after('title');

            $table->index(['category', 'status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['category', 'status', 'published_at']);
            $table->dropColumn('category');
        });
    }
};
