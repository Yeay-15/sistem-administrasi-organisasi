<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Grup turnamen (mis. "Grup A", "Grup B") — mekanisme pembagian grup
     * & jumlah tim per grup ditentukan federasi/panitia, bukan sistem,
     * jadi tabel ini sengaja sangat sederhana: hanya nama & urutan tampil.
     */
    public function up(): void
    {
        Schema::create('event_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_groups');
    }
};
