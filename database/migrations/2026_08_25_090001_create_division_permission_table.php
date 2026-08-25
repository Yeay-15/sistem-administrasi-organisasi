<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pivot ini memberi Divisi (bukan Role) hak akses tambahan yang tidak
        // tergantung pada peran anggotanya. Contoh: Divisi Infokom diberi hak
        // 'manage_news' & 'manage_gallery' secara langsung di sini, sehingga
        // TIDAK ADA nama divisi yang di-hardcode di kode aplikasi — cukup
        // toggle lewat halaman Manajemen Peran & Akses.
        Schema::create('division_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['division_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_permission');
    }
};
