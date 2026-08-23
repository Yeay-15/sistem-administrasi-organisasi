<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description'];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method agar mencatat log jadi sangat mudah
    public static function record($action, $description)
    {
        if (Auth::check()) {
            self::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
            ]);
        }
    }
}
