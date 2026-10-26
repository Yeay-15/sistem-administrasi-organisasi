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
        'description',
    ];

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }
}
