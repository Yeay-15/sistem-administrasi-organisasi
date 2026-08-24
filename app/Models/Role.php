<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Daftar hak akses (permission) yang dimiliki peran ini.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Daftar akun user yang memakai peran ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Super Admin selalu memiliki akses penuh ke seluruh sistem,
     * terlepas dari apa yang tercatat di tabel permission_role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->slug === 'super-admin';
    }
}
