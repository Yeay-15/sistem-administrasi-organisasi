<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Committee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'start_date',
        'end_date',
        'description',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Baris pivot (dengan peran & bidang) anggota kepanitiaan ini.
     * Dipakai (bukan belongsToMany biasa) karena kita perlu akses ke
     * position_category & panitia_bidang_id per baris, bukan cuma daftar Member.
     */
    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    /**
     * Daftar Member unik yang tergabung di kepanitiaan ini (lintas peran).
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'committee_members')
            ->withPivot(['id', 'position_category', 'panitia_bidang_id', 'notes'])
            ->withTimestamps();
    }

    /**
     * Agenda (rapat persiapan + hari-H) yang terhubung ke kepanitiaan ini.
     */
    public function agendas(): BelongsToMany
    {
        return $this->belongsToMany(Agenda::class, 'committee_agenda')->withTimestamps();
    }

    /**
     * Urutan tampilan peran teras panitia -> bidang, dipakai untuk
     * mengelompokkan & mengurutkan daftar anggota di halaman detail.
     */
    public static function positionCategories(): array
    {
        return [
            'Ketua Panitia',
            'Sekretaris Panitia',
            'Bendahara Panitia',
            'Bendahara Peserta',
            'Ketua Bidang',
            'Anggota Bidang',
        ];
    }
}
