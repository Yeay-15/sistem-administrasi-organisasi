<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'path',
        'route_name',
        'visitor_key',
        'referrer',
    ];
}
