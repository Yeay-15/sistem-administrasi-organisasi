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
    protected ?Collection $cachedRolePermissionSlugs = null;

    /**
     * Cache hak akses tambahan dari Divisi (lihat Division::permissions()).
     */
    protected ?Collection $cachedDivisionPermissionSlugs = null;

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
     *
     * Sebuah akses diizinkan jika salah satu dari ini benar:
     * 1. Peran (Role) pengguna memiliki permission tersebut secara langsung.
     * 2. Divisi (Division) pengguna memiliki permission tersebut secara langsung
     *    (lihat Division::permissions() — dipakai untuk kasus seperti Infokom
     *    yang boleh mengelola Berita/Galeri tanpa perlu peran khusus).
     * 3. Untuk permission bertipe 'view_xxx': secara otomatis diizinkan jika
     *    pengguna sudah memiliki 'manage_xxx', karena hak Kelola sudah pasti
     *    mencakup hak Lihat.
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->rolePermissionSlugs()->contains($slug) || $this->divisionPermissionSlugs()->contains($slug)) {
            return true;
        }

        if (str_starts_with($slug, 'view_')) {
            $manageSlug = 'manage_' . substr($slug, 5);

            return $this->rolePermissionSlugs()->contains($manageSlug)
                || $this->divisionPermissionSlugs()->contains($manageSlug);
        }

        return false;
    }

    private function rolePermissionSlugs(): Collection
    {
        if ($this->cachedRolePermissionSlugs === null) {
            $this->cachedRolePermissionSlugs = $this->role_id && $this->role
                ? $this->role->permissions->pluck('slug')
                : collect();
        }

        return $this->cachedRolePermissionSlugs;
    }

    private function divisionPermissionSlugs(): Collection
    {
        if ($this->cachedDivisionPermissionSlugs === null) {
            $this->cachedDivisionPermissionSlugs = $this->member?->division
                ? $this->member->division->permissions->pluck('slug')
                : collect();
        }

        return $this->cachedDivisionPermissionSlugs;
    }
}
