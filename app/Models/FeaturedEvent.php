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

    public function teams(): HasMany
    {
        return $this->hasMany(EventTeam::class)->orderBy('name');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(EventGroup::class)->orderBy('order')->orderBy('name');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EventMatch::class)->orderBy('round_order');
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
     * Semua pertandingan event ini, dikelompokkan per fase (lihat
     * EventMatch::STAGES) mengikuti urutan alur turnamen — fase grup
     * (dipecah lagi per grup lewat groupsWithStandings()) dan babak-babak
     * gugur setelahnya. Fase yang tidak dipakai (tidak ada pertandingan
     * di dalamnya) otomatis tidak muncul.
     */
    public function knockoutMatchesByStage()
    {
        $matches = $this->matches()->whereNot('stage', 'group')->with(['team1', 'team2', 'winner'])->get();

        return collect(EventMatch::STAGES)
            ->except('group')
            ->map(fn ($label, $stage) => [
                'stage' => $stage,
                'label' => $label,
                'matches' => $matches->where('stage', $stage)->values(),
            ])
            ->filter(fn ($data) => $data['matches']->isNotEmpty())
            ->values();
    }

    /**
     * Semua grup beserta daftar tim & klasemennya masing-masing — siap
     * pakai baik untuk halaman admin (Kelola Bagan) maupun publik
     * (microsite event).
     */
    public function groupsWithStandings()
    {
        return $this->groups()->with('teams')->get()->map(fn ($group) => [
            'group' => $group,
            'standings' => $group->standings(),
        ]);
    }
}
