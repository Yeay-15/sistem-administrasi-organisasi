<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PanitiaBidang extends Model
{
    protected $table = 'panitia_bidang';

    protected $fillable = [
        'name',
        'description',
    ];

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }
}
