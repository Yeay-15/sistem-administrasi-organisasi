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
        'committee_field_id',
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

    public function committeeField(): BelongsTo
    {
        return $this->belongsTo(CommitteeField::class);
    }

    /**
     * True jika peran ini termasuk "teras panitia" (Ketua/Sekretaris/Bendahara),
     * bukan peran berbasis bidang (Ketua Bidang/Anggota Bidang).
     */
    public function isCommitteeLeadership(): bool
    {
        return in_array($this->position_category, [
            'Ketua Panitia',
            'Sekretaris Panitia',
            'Bendahara Panitia',
            'Bendahara Peserta',
        ], true);
    }
}
