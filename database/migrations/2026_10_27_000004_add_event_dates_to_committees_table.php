<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * start_date/end_date = masa aktif kepanitiaan (rapat pembentukan s.d. rapat evaluasi).
     * event_dates = tanggal pelaksanaan ACARA-nya (boleh beberapa hari / terpisah),
     * disimpan sebagai array JSON "Y-m-d". Dipakai Surat Generator.
     */
    public function up(): void
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->json('event_dates')->nullable()->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->dropColumn('event_dates');
        });
    }
};
