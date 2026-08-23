<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda extends Model
{
    // Mengizinkan mass assignment untuk kolom-kolom ini
    protected $fillable = [
        'agenda_code',
        'name',
        'date',
        'type',
        'person_in_charge',
        'notes',
        'status',
    ];

    // Relasi One-to-Many: 1 Agenda memiliki banyak Absensi
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }
}
