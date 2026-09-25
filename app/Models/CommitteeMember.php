<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    protected $fillable = [
        'committee_id',
        'member_id',
        'position_category',
        'panitia_bidang_id',
        'notes',
    ];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(PanitiaBidang::class, 'panitia_bidang_id');
    }

    /**
     * True jika peran ini termasuk "teras panitia" (Ketua/Sekretaris/Bendahara),
     * bukan peran berbasis bidang (Ketua Bidang/Anggota Bidang).
     */
    public function isTerasPanitia(): bool
    {
        return in_array($this->position_category, [
            'Ketua Panitia',
            'Sekretaris Panitia',
            'Bendahara Panitia',
            'Bendahara Peserta',
        ], true);
    }
}
