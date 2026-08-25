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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('photo_path')->nullable(); // Foto dokumentasi/sertifikat prestasi
            $table->string('title'); // Contoh: "Juara 2 Debat B. Inggris"
            $table->string('achiever_name'); // Nama mahasiswa/pengurus peraih prestasi
            $table->string('description')->nullable(); // Tingkat/keterangan, contoh: "DISPORASENI NASIONAL 2025"
            $table->unsignedInteger('order')->default(0); // Urutan tampil di grid Beranda
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
