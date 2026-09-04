<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('logo_path')->nullable();
            // Seed menentukan urutan/slot awal di bagan (1..team_count).
            // Nullable karena tim bisa ditambahkan dulu sebelum bagan
            // digenerate (baru diisi otomatis saat generate bracket).
            $table->unsignedSmallInteger('seed')->nullable();
            $table->timestamps();

            $table->unique(['featured_event_id', 'seed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_teams');
    }
};
