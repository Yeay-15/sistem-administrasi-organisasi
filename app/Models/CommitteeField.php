<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Daftar master nama bidang/seksi kepanitiaan (mis. "Sie Acara", "Sie
// Konsumsi"), dipakai berulang lintas kepanitiaan supaya penamaannya
// konsisten. Sebelumnya bernama PanitiaBidang.
class CommitteeField extends Model
{
    protected $table = 'committee_fields';

    protected $fillable = [
        'name',
        'sort_order',
        'description',
    ];

    /** Urutan awal bidang baru yang namanya dikenal (kelipatan 10). Selain ini = 100. */
    private const PRESET_ORDER = [
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

    /** Urutan bawaan untuk sebuah nama bidang ("Sie Acara" == "Acara"). */
    public static function presetOrder(string $name): int
    {
        $key = trim(preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower($name)));
        $key = trim(preg_replace('/^(sie|seksi|bidang)\s+/', '', $key));

        return self::PRESET_ORDER[$key] ?? 100;
    }

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }
}
