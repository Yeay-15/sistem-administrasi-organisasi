<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu baris = satu pertandingan di bagan gugur tunggal (single
     * elimination). 'round' dibuat berupa angka (1 = babak pertama, terus
     * naik sampai final) alih-alih enum bernama tetap (R32/QF/SF/dst) —
     * supaya bagan tetap benar untuk event dengan jumlah tim berapa pun
     * (8/16/32/64), bukan cuma untuk 32 tim. Label babak (mis. "Perempat
     * Final") dihitung ulang saat ditampilkan lewat FeaturedEvent::roundLabel().
     *
     * 'round_order' adalah posisi pertandingan di dalam babak tsb (1-based).
     * Kombinasi (round, round_order) dipakai EventMatch::propagateWinner()
     * untuk otomatis mengisi slot tim di pertandingan babak berikutnya begitu
     * sebuah pertandingan selesai diinput skornya.
     */
    public function up(): void
    {
        Schema::create('event_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_event_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('round');
            $table->unsignedSmallInteger('round_order');

            $table->foreignId('team1_id')->nullable()->constrained('event_teams')->nullOnDelete();
            $table->foreignId('team2_id')->nullable()->constrained('event_teams')->nullOnDelete();
            $table->unsignedSmallInteger('team1_score')->nullable();
            $table->unsignedSmallInteger('team2_score')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('event_teams')->nullOnDelete();

            $table->dateTime('scheduled_at')->nullable();
            $table->string('venue')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'finished'])->default('scheduled');
            // Catatan singkat, mis. "Menang WO", "Adu penalti 4-2".
            $table->string('notes')->nullable();

            $table->timestamps();

            $table->unique(['featured_event_id', 'round', 'round_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_matches');
    }
};
