<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeaturedEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'poster_path',
        'short_description',
        'content',
        'location',
        'event_start_date',
        'event_end_date',
        'registration_url',
        'cta_label',
        'has_bracket',
        'team_count',
        'status',
        'show_on_homepage',
        'show_announcement_bar',
    ];

    protected $casts = [
        'event_start_date' => 'date',
        'event_end_date' => 'date',
        'has_bracket' => 'boolean',
        'show_on_homepage' => 'boolean',
        'show_announcement_bar' => 'boolean',
    ];

    public const STATUSES = [
        'draft' => 'Draft (belum tampil ke publik)',
        'active' => 'Aktif',
        'archived' => 'Arsip',
    ];

    // Ukuran bagan yang didukung — harus pangkat 2 supaya sistem gugur
    // tunggal tidak perlu bye/kosong di tengah bagan.
    public const TEAM_COUNTS = [8, 16, 32, 64];

    public function teams(): HasMany
    {
        return $this->hasMany(EventTeam::class)->orderBy('seed');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EventMatch::class)->orderBy('round')->orderBy('round_order');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(EventUpdate::class)->latest('published_at');
    }

    /**
     * Event yang boleh diakses lewat halaman publik: statusnya aktif ATAU
     * arsip (arsip tetap bisa dibuka untuk rekam jejak), draft tidak.
     */
    public function scopeVisibleToPublic($query)
    {
        return $query->whereIn('status', ['active', 'archived']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Dipakai untuk hero banner beranda — hanya event aktif yang memang
     * ditandai admin untuk tampil di beranda.
     */
    public function scopeOnHomepage($query)
    {
        return $query->where('status', 'active')->where('show_on_homepage', true);
    }

    /**
     * Dipakai untuk pita pengumuman global (tampil di semua halaman
     * publik) — ditandai terpisah dari show_on_homepage supaya admin bisa
     * memilih salah satu, keduanya, atau tidak sama sekali.
     */
    public function scopeWithAnnouncementBar($query)
    {
        return $query->where('status', 'active')->where('show_announcement_bar', true);
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->poster_path ? asset('storage/' . $this->poster_path) : null;
    }

    /**
     * Total babak di bagan gugur tunggal berdasarkan jumlah tim
     * (log2 dari team_count). 32 tim -> 5 babak (32→16→8→4→2→1 juara).
     */
    public function totalRounds(): int
    {
        if (! $this->has_bracket || ! $this->team_count) {
            return 0;
        }

        return (int) log($this->team_count, 2);
    }

    /**
     * Label babak yang enak dibaca manusia, dihitung dari posisi babak
     * relatif terhadap total babak — supaya benar untuk 8, 16, 32, atau 64
     * tim sekaligus, tanpa perlu tabel enum nama babak yang kaku.
     */
    public static function roundLabel(int $round, int $totalRounds): string
    {
        $fromFinal = $totalRounds - $round;

        return match (true) {
            $fromFinal === 0 => 'Final',
            $fromFinal === 1 => 'Semifinal',
            $fromFinal === 2 => 'Perempat Final',
            default => 'Babak ' . (2 ** ($fromFinal + 1)) . ' Besar',
        };
    }

    /**
     * Data bagan siap-tampil, dikelompokkan per babak & sudah membawa
     * label babaknya masing-masing. Dipakai bersama oleh halaman kelola
     * bagan (admin) dan microsite publik supaya tampilannya selalu
     * konsisten.
     */
    public function bracketRounds()
    {
        $totalRounds = $this->totalRounds();

        if ($totalRounds === 0) {
            return collect();
        }

        return $this->matches()
            ->with(['team1', 'team2', 'winner'])
            ->get()
            ->groupBy('round')
            ->map(function ($matches, $round) use ($totalRounds) {
                return [
                    'round' => $round,
                    'label' => static::roundLabel((int) $round, $totalRounds),
                    'matches' => $matches->sortBy('round_order')->values(),
                ];
            })
            ->sortKeys()
            ->values();
    }
}
