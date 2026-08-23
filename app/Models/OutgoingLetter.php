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
}
