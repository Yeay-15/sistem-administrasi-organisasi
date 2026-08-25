<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspiration extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'category',
        'message',
        'is_anonymous',
        'is_read',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_read' => 'boolean',
    ];

    /**
     * Nama tampilan pengirim — menyembunyikan identitas jika dikirim secara anonim,
     * meski data aslinya tetap tersimpan di database untuk keperluan tindak lanjut internal.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->is_anonymous ? 'Anonim' : ($this->name ?: 'Tanpa Nama');
    }
}
