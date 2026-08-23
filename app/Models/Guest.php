<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['agenda_id', 'name', 'institution', 'role', 'contact'];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
