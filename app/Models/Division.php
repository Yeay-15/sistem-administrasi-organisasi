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

    /**
     * Hak akses tambahan yang dimiliki Divisi ini secara langsung, terlepas
     * dari peran (Role) masing-masing anggotanya. Lihat migration
     * 'create_division_permission_table' untuk konteks lengkap.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'division_permission');
    }
}
