<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi dari model "bagan gugur-tunggal 8/16/32/64 tim dengan
     * pairing otomatis" menjadi model "turnamen berbasis fase" yang lebih
     * sesuai kondisi nyata: jumlah tim mengikuti berapa yang mendaftar
     * (tidak harus pangkat 2), diawali fase grup dengan klasemen, baru
     * lanjut ke babak gugur (16 besar/8 besar/semifinal/final/perebutan
     * juara 3) — dan pasangan pertandingan di SETIAP fase diisi manual
     * oleh admin (lihat EventBracketController::storeMatch), bukan
     * digenerate otomatis oleh sistem.
     */
    public function up(): void
    {
        // MySQL memakai index unique (featured_event_id, round, round_order)
        // sebagai index pendukung foreign key kolom featured_event_id
        // (karena kolom itu jadi kolom pertama di index composite-nya).
        // Index unique itu TIDAK BISA langsung dihapus selama masih jadi
        // satu-satunya index pendukung FK tsb — jadi kita buat dulu index
        // biasa (non-unique) khusus untuk featured_event_id di langkah
        // terpisah, baru unique index composite-nya aman dihapus.
        Schema::table('event_matches', function (Blueprint $table) {
            $table->index('featured_event_id', 'event_matches_featured_event_id_idx');
        });

        Schema::table('event_matches', function (Blueprint $table) {
            $table->dropUnique(['featured_event_id', 'round', 'round_order']);
            $table->dropColumn('round');
        });

        Schema::table('event_matches', function (Blueprint $table) {
            // Nilai stage lihat EventMatch::STAGES (group, ro32, ro16, qf, sf, third_place, final).
            $table->string('stage')->default('group')->after('featured_event_id');
            // Hanya diisi untuk stage='group' — menunjuk grup mana yang klasemennya dipengaruhi pertandingan ini.
            // cascadeOnDelete: kalau grupnya dihapus, pertandingan fase grup itu ikut terhapus (tidak relevan lagi tanpa grupnya).
            $table->foreignId('event_group_id')->nullable()->after('stage')
                ->constrained('event_groups')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_matches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_group_id');
            $table->dropColumn('stage');
        });

        Schema::table('event_matches', function (Blueprint $table) {
            $table->unsignedTinyInteger('round')->default(1)->after('featured_event_id');
            $table->unique(['featured_event_id', 'round', 'round_order']);
        });

        Schema::table('event_matches', function (Blueprint $table) {
            $table->dropIndex('event_matches_featured_event_id_idx');
        });
    }
};
