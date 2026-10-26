<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Merapikan penamaan: tabel `panitia_bidang` (daftar master bidang/seksi
     * kepanitiaan, mis. "Sie Acara") & kolom FK `panitia_bidang_id` di
     * committee_members diganti ke Inggris. Pakai rename(), bukan
     * create+drop, supaya data yang sudah ada (kalau migration lama sudah
     * pernah dijalankan) tetap utuh.
     */
    public function up(): void
    {
        Schema::rename('panitia_bidang', 'committee_fields');

        Schema::table('committee_members', function (Blueprint $table) {
            $table->renameColumn('panitia_bidang_id', 'committee_field_id');
        });
    }

    public function down(): void
    {
        Schema::table('committee_members', function (Blueprint $table) {
            $table->renameColumn('committee_field_id', 'panitia_bidang_id');
        });

        Schema::rename('committee_fields', 'panitia_bidang');
    }
};
