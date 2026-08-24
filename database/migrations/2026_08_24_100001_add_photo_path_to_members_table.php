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
        Schema::table('members', function (Blueprint $table) {
            // Path relatif foto pengurus di dalam disk 'public' (mis. members/xxx.jpg).
            // Nullable karena tidak semua pengurus (terutama data lama) sudah punya foto.
            $table->string('photo_path')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
