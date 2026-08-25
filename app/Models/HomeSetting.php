<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSetting extends Model
{
    protected $fillable = [
        'hero_image_path',
        'hero_title',
        'hero_subtitle',
        'chairman_name',
        'chairman_photo_path',
        'chairman_message',
        'aspiration_mode',
    ];

    public const ASPIRATION_MODES = [
        'public' => 'Publik (Terbuka untuk siapa saja)',
        'pengurus_only' => 'Hanya Pengurus (Wajib Login)',
        'nonaktif' => 'Nonaktif (Sembunyikan Formulir)',
    ];

    /**
     * Pengaturan Beranda bersifat singleton — selalu hanya ada 1 baris data
     * (id = 1). Method ini mengambil baris tersebut, atau membuatnya dengan
     * nilai kosong jika belum pernah diisi sama sekali.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image_path ? asset('storage/' . $this->hero_image_path) : null;
    }

    public function getChairmanPhotoUrlAttribute(): ?string
    {
        return $this->chairman_photo_path ? asset('storage/' . $this->chairman_photo_path) : null;
    }
}
