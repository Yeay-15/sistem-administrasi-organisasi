<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Satu kepanitiaan mewakili satu acara/event yang punya struktur panitia
     * sendiri (Ketua, Sekretaris, Bendahara, Bidang-bidang). Berdiri sendiri
     * dari tabel `agendas` (tidak wajib 1:1) karena satu kepanitiaan biasanya
     * mencakup serangkaian rapat persiapan + hari-H — hubungan ke agenda
     * diatur lewat tabel pivot `committee_agenda` (many-to-many).
     */
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable(); // mis. Internal, Kerjasama, Kompetisi
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('Persiapan'); // Persiapan, Berjalan, Selesai
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committees');
    }
};
