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
            // KATIBER adalah paguyuban lintas kampus, sehingga jurusan & universitas
            // asal masing-masing pengurus perlu dicatat dan ditampilkan di Portal Publik.
            $table->string('major')->nullable()->after('batch');
            $table->string('university')->nullable()->after('major');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['major', 'university']);
        });
    }
};
