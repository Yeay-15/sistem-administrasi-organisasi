<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutgoingLetter extends Model
{
    // Tabel surat keluar berdiri sendiri.
    protected $fillable = [
        'reference_number',
        'date',
        'type',
        'subject',
        'destination',
        'signatory',
        'status',
        'file_path',
    ];

    // committee_id, letter_template_id & payload diisi oleh Surat Generator
    // (sengaja tidak di $fillable supaya tidak bisa disusupkan lewat form manual).
    protected $casts = [
        'payload' => 'array',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function letterTemplate()
    {
        return $this->belongsTo(LetterTemplate::class);
    }

    /** True bila surat ini dibuat lewat Surat Generator (.docx bisa diunduh ulang). */
    public function isGenerated(): bool
    {
        return $this->letter_template_id !== null && ! empty($this->payload);
    }
}
