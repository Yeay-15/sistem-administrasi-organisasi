<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            return;
        }

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // Contoh: manage_agendas, view_dashboard
            $table->string('label'); // Label yang ditampilkan di UI, contoh: "Kelola Agenda"
            $table->string('group')->nullable(); // Untuk mengelompokkan di UI, contoh: "Kegiatan"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
