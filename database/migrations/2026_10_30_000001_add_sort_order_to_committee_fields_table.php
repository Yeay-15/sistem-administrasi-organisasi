<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Urutan awal (kelipatan 10 supaya mudah disisipi). Kunci = nama bidang tanpa "Sie", huruf kecil. */
    private const PRESET = [
        'korlap' => 10,
        'acara' => 20,
        'perlengkapan' => 30,
        'konsumsi' => 40,
        'medis' => 50,
        'publikasi dan dokumentasi' => 60,
        'keamanan' => 70,
        'transportasi' => 80,
        'keamanan transportasi' => 90,
        'keamanan dan transportasi' => 90,
    ];

    public function up(): void
    {
        Schema::table('committee_fields', function (Blueprint $table) {
            // Bidang yang tidak ada di PRESET (mis. Administrasi, Sponsorship) = 100 -> di bawah, urut abjad.
            $table->unsignedSmallInteger('sort_order')->default(100)->after('name');
        });

        foreach (DB::table('committee_fields')->get(['id', 'name']) as $field) {
            $key = trim(preg_replace('/^(sie|seksi|bidang)\s+/', '', trim(preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower($field->name)))));
            if (isset(self::PRESET[$key])) {
                DB::table('committee_fields')->where('id', $field->id)->update(['sort_order' => self::PRESET[$key]]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('committee_fields', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
