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
