<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Timeline "Info Terkini" di halaman detail event — dipakai untuk
     * pengumuman technical meeting, hasil rekap harian, info venue
     * berubah, dsb. Terpisah dari bagan (event_matches) karena tidak
     * semua update berbentuk skor pertandingan.
     */
    public function up(): void
    {
        Schema::create('event_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_event_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_updates');
    }
};
