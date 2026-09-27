<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pivot agenda <-> divisi.
     *
     * Hanya diisi ketika agendas.attendance_scope = 'division'. Bisa berisi
     * lebih dari satu baris per agenda untuk kasus kolaborasi lintas divisi
     * (mis. Humas x Infokom), sehingga daftar absensi & statistik kehadiran
     * cukup menggabungkan anggota dari semua divisi yang tercatat di sini.
     */
    public function up(): void
    {
        Schema::create('agenda_division', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['agenda_id', 'division_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_division');
    }
};
