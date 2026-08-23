<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'abbreviation'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
