<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outgoing_letters', function (Blueprint $table) {
            $table->foreignId('committee_id')->nullable()->after('file_path')
                ->constrained('committees')->nullOnDelete();
            $table->foreignId('letter_template_id')->nullable()->after('committee_id')
                ->constrained('letter_templates')->nullOnDelete();
            // Snapshot semua isian saat surat dibuat -> .docx bisa diunduh ulang
            // persis sama walau data kepanitiaan berubah kemudian.
            $table->json('payload')->nullable()->after('letter_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('outgoing_letters', function (Blueprint $table) {
            $table->dropConstrainedForeignId('letter_template_id');
            $table->dropConstrainedForeignId('committee_id');
            $table->dropColumn('payload');
        });
    }
};
