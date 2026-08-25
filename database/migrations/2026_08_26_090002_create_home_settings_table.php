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
        // Tabel ini sengaja dirancang sebagai "singleton" (selalu hanya 1 baris,
        // lihat App\Models\HomeSetting::current()) untuk menyimpan konten dinamis
        // di halaman Beranda publik: Hero & Sambutan Ketua Umum.
        Schema::create('home_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image_path')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('chairman_name')->nullable();
            $table->string('chairman_photo_path')->nullable();
            $table->text('chairman_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_settings');
    }
};
