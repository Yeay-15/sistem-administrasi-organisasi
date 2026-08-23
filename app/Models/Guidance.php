<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guidance extends Model
{
    protected $fillable = [
        'member_id',
        'date',
        'type',
        'reason',
        'notes',
        'counselor',
        'status',
    ];

    // Relasi Many-to-One: Pembinaan ini ditujukan untuk 1 Pengurus
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
