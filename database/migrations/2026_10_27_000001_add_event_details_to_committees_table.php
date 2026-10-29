<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data acara yang dipakai berulang di banyak surat (preset "Group Acara").
     * Nama acara & tanggal sudah ada di kolom `name`, `start_date`, `end_date`.
     */
    public function up(): void
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->string('theme')->nullable()->after('type');       // tema kegiatan
            $table->string('venue')->nullable()->after('theme');      // tempat/lokasi
            $table->time('start_time')->nullable()->after('end_date'); // jam mulai
        });
    }

    public function down(): void
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->dropColumn(['theme', 'venue', 'start_time']);
        });
    }
};
