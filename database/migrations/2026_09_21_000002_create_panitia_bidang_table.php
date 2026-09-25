<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Daftar master nama bidang/seksi kepanitiaan (mis. "Sie Acara",
     * "Sie Konsumsi"), dipakai berulang lintas kepanitiaan supaya
     * penamaannya konsisten (tidak beda-beda ejaan tiap event dibuat).
     */
    public function up(): void
    {
        Schema::create('panitia_bidang', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panitia_bidang');
    }
};
