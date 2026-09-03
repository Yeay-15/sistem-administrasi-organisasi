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
        // Penguat di level database untuk aturan "satu pengurus maksimal satu
        // akun login" — sebelumnya cuma dijaga lewat validasi aplikasi di
        // RoleManagementController::storeUser(). Kolom ini nullable (banyak
        // baris NULL tetap diperbolehkan oleh index unique), jadi tidak
        // memengaruhi akun lama yang belum terhubung ke data pengurus.
        Schema::table('users', function (Blueprint $table) {
            $table->unique('member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['member_id']);
        });
    }
};
