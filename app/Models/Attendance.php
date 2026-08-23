<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'agenda_id',
        'member_id',
        'status',
        'attendance_time',
        'notes',
    ];

    // Relasi Many-to-One: Absensi ini milik 1 Agenda
    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    // Relasi Many-to-One: Absensi ini milik 1 Pengurus
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
