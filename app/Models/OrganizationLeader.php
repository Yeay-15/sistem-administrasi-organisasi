<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationLeader extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'photo_path',
        'name',
        'major',
        'period_start',
        'period_end',
        'order',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->period_start . ' – ' . $this->period_end;
    }
}
