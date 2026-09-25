<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menghubungkan satu kepanitiaan ke banyak agenda (rapat-rapat
     * persiapan, technical meeting, sampai hari-H acaranya) supaya statistik
     * kehadiran panitia bisa dihitung khusus dari agenda-agenda milik
     * kepanitiaan tersebut, terpisah dari agenda organisasi secara umum.
     */
    public function up(): void
    {
        Schema::create('committee_agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained('committees')->cascadeOnDelete();
            $table->foreignId('agenda_id')->constrained('agendas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['committee_id', 'agenda_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_agenda');
    }
};
