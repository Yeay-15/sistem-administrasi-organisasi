<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_teams', function (Blueprint $table) {
            // Nullable — tim bisa ditambahkan dulu sebelum grup dibuat,
            // atau untuk event yang tidak memakai fase grup sama sekali.
            $table->foreignId('event_group_id')->nullable()->after('featured_event_id')
                ->constrained('event_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_group_id');
        });
    }
};
