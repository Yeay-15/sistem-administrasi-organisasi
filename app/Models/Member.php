<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'student_id',
        'batch',
        'major',
        'university',
        'division_id',
        'position',
        'status',
        'membership_type',
        'photo_path',
        'join_date',
        'exit_date',
        'notes'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Posisi orang ini di dalam divisinya untuk keperluan hak akses divisi:
     * 'kadiv' (Ketua Divisi), 'sekdiv' (Sekretaris Divisi), selain itu 'anggota'
     * (termasuk jabatan BPH seperti Ketua/Sekretaris/Bendahara Umum — hak akses
     * mereka diatur lewat Peran, bukan lewat hak akses divisi).
     */
    public function divisionScope(): string
    {
        return match ($this->position) {
            'Ketua Divisi' => 'kadiv',
            'Sekretaris Divisi' => 'sekdiv',
            default => 'anggota',
        };
    }

    public function isPengurus(): bool
    {
        return $this->membership_type === 'Pengurus';
    }

    /**
     * Baris pivot (dengan peran & bidang) histori kepanitiaan orang ini.
     */
    public function committeeMemberships()
    {
        return $this->hasMany(CommitteeMember::class);
    }

    /**
     * Daftar Kepanitiaan unik yang pernah diikuti (lintas peran).
     */
    public function committees()
    {
        return $this->belongsToMany(Committee::class, 'committee_members')
            ->withPivot(['id', 'position_category', 'committee_field_id', 'notes'])
            ->withTimestamps();
    }

    /**
     * URL publik foto pengurus. Mengembalikan null jika belum ada foto,
     * sehingga tampilan bisa fallback ke inisial/avatar default.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function guidances()
    {
        return $this->hasMany(Guidance::class);
    }

    // Akun login (users) yang terhubung dengan pengurus ini, jika ada
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
