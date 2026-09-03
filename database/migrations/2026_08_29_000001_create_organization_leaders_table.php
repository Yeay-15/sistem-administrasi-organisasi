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
        Schema::create('organization_leaders', function (Blueprint $table) {
            $table->id();
            $table->string('photo_path')->nullable(); // Foto Ketua Umum, fallback ke siluet/logo bila kosong
            $table->string('name'); // Nama Ketua Umum
            $table->string('major')->nullable(); // Jurusan/Kampus — sengaja opsional, boleh dikosongkan dulu
            $table->string('period_start'); // Tahun mulai menjabat, contoh: "2014"
            $table->string('period_end'); // Tahun akhir menjabat, contoh: "2016"
            $table->unsignedInteger('order')->default(0); // Urutan tampil di timeline "Estafet Kepemimpinan"
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_leaders');
    }
};
