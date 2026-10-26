<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel inventaris barang organisasi (alat, perlengkapan, obat P3K, spanduk, dst).
     *
     * Jumlah dipisah dari satuan (quantity + unit) supaya data seperti "1 gulung",
     * "10 tablet", "200 g", atau "0,5 plastik" tetap terbaca rapi di tabel DAN
     * quantity-nya tetap berupa angka (bisa dijumlah/diurutkan).
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama barang
            $table->decimal('quantity', 10, 2)->default(1); // Jumlah (boleh desimal, mis. 0,5)
            $table->string('unit', 50)->nullable(); // Satuan: buah, set, gulung, tablet, dst
            $table->string('location', 100)->nullable()->index(); // Tempat penyimpanan / pemegang
            $table->date('expired_at')->nullable()->index(); // Tanggal kedaluwarsa (obat, dst)
            $table->text('notes')->nullable(); // Keterangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
