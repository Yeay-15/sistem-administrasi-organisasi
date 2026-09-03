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
        // Menambahkan identitas visual (logo) dan kanal kontak/media sosial
        // yang dipakai bersama oleh navbar & footer di seluruh halaman publik.
        Schema::table('home_settings', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('hero_image_path');
            $table->string('instagram_url')->nullable()->after('chairman_message');
            $table->string('whatsapp_number')->nullable()->after('instagram_url');
            $table->string('contact_email')->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'instagram_url', 'whatsapp_number', 'contact_email']);
        });
    }
};
