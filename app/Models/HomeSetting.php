<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSetting extends Model
{
    protected $fillable = [
        'logo_path',
        'hero_image_path',
        'hero_title',
        'hero_subtitle',
        'chairman_name',
        'chairman_photo_path',
        'chairman_message',
        'instagram_url',
        'whatsapp_number',
        'contact_email',
        'aspiration_mode',
        'show_leaders_public',
    ];

    protected $casts = [
        'show_leaders_public' => 'boolean',
    ];

    public const ASPIRATION_MODES = [
        'public' => 'Publik (Terbuka untuk siapa saja)',
        'pengurus_only' => 'Hanya Pengurus (Wajib Login)',
        'nonaktif' => 'Nonaktif (Sembunyikan Formulir)',
    ];

    /**
     * Pengaturan Beranda bersifat singleton - selalu hanya ada 1 baris data
     * (id = 1). Method ini mengambil baris tersebut, atau membuatnya dengan
     * nilai kosong jika belum pernah diisi sama sekali.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image_path ? asset('storage/' . $this->hero_image_path) : null;
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        if (! $this->whatsapp_number) {
            return null;
        }

        return 'https://wa.me/' . preg_replace('/\D/', '', $this->whatsapp_number);
    }

    /**
     * Tautan email resmi.
     * Untuk alamat Gmail, arahkan langsung ke jendela tulis Gmail di browser
     * (memakai akun Google yang sedang aktif), bukan mailto: yang memicu
     * pemilihan profil Chrome / aplikasi email bawaan lalu gagal membuka Gmail.
     * Alamat non-Gmail tetap memakai mailto:.
     */
    public function getEmailLinkAttribute(): ?string
    {
        $email = trim((string) $this->contact_email);

        if ($email === '') {
            return null;
        }

        if ($this->email_link_is_web) {
            return 'https://mail.google.com/mail/?view=cm&fs=1&to=' . rawurlencode($email);
        }

        return 'mailto:' . $email;
    }

    public function getEmailLinkIsWebAttribute(): bool
    {
        return (bool) preg_match('/@(gmail|googlemail)\.com$/i', trim((string) $this->contact_email));
    }

    public function getChairmanPhotoUrlAttribute(): ?string
    {
        return $this->chairman_photo_path ? asset('storage/' . $this->chairman_photo_path) : null;
    }
}
