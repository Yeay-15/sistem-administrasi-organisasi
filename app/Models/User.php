<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password', 'role_id', 'member_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Cache hak akses (permission) milik user selama satu request,
     * supaya hasPermission() tidak query berulang-ulang.
     */
    protected ?Collection $cachedPermissionSlugs = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Peran (role) yang menentukan hak akses akun ini.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Data pengurus (member) yang terhubung dengan akun login ini.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Super Admin selalu memiliki akses penuh ke seluruh sistem.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super-admin';
    }

    /**
     * Cek apakah user memiliki hak akses tertentu berdasarkan slug permission,
     * contoh: $user->hasPermission('manage_agendas').
     */
    public function hasPermission(string $slug): bool
    {
        if (! $this->role_id) {
            return false;
        }

        if ($this->cachedPermissionSlugs === null) {
            $this->cachedPermissionSlugs = $this->role
                ? $this->role->permissions->pluck('slug')
                : collect();
        }

        return $this->cachedPermissionSlugs->contains($slug);
    }
}
