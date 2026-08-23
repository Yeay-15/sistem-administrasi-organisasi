<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingLetter extends Model
{
    // Tabel surat masuk berdiri sendiri, tidak ada relasi langsung (BelongsTo/HasMany) 
    // dengan entitas SDM/Absensi.
    protected $fillable = [
        'received_date',
        'reference_number',
        'sender',
        'subject',
        'addressed_to',
        'notes',
        'file_path',
    ];
}
