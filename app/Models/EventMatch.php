<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMatch extends Model
{
    protected $fillable = [
        'featured_event_id',
        'stage',
        'event_group_id',
        'round_order',
        'team1_id',
        'team2_id',
        'team1_score',
        'team2_score',
        'winner_id',
        'scheduled_at',
        'venue',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'team1_score' => 'integer',
        'team2_score' => 'integer',
    ];

    /**
     * Fase pertandingan, diurutkan sesuai alur turnamen nyata: fase grup
     * dulu (dengan klasemen), baru babak gugur. Jumlah tim yang lolos ke
     * tiap babak gugur ditentukan federasi/panitia (bisa langsung ke 8
     * besar tanpa 16 besar, dsb.) — jadi admin bebas memilih fase mana
     * saja yang dipakai, tidak wajib berurutan penuh.
     */
    public const STAGES = [
        'group' => 'Fase Grup',
        'ro32' => '32 Besar',
        'ro16' => '16 Besar',
        'qf' => 'Perempat Final',
        'sf' => 'Semifinal',
        'third_place' => 'Perebutan Juara 3',
        'final' => 'Final',
    ];

    public function featuredEvent(): BelongsTo
    {
        return $this->belongsTo(FeaturedEvent::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(EventGroup::class, 'event_group_id');
    }

    public function team1(): BelongsTo
    {
        return $this->belongsTo(EventTeam::class, 'team1_id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(EventTeam::class, 'team2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(EventTeam::class, 'winner_id');
    }

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }
}
