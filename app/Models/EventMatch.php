<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMatch extends Model
{
    protected $fillable = [
        'featured_event_id',
        'round',
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
    ];

    public function featuredEvent(): BelongsTo
    {
        return $this->belongsTo(FeaturedEvent::class);
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

    /**
     * Begitu sebuah pertandingan ditandai selesai dengan pemenang, otomatis
     * dorong tim tersebut ke slot yang sesuai di pertandingan babak
     * berikutnya — supaya admin tidak perlu mengisi manual satu per satu
     * naik ke atas bagan.
     *
     * Aturan slot: dua pertandingan berurutan di babak sekarang (order
     * ganjil & genap sesudahnya) selalu bermuara ke SATU pertandingan yang
     * sama di babak berikutnya. Order ganjil mengisi slot tim1, order
     * genap mengisi slot tim2.
     */
    public function propagateWinner(): void
    {
        if (! $this->winner_id) {
            return;
        }

        $totalRounds = $this->featuredEvent->totalRounds();

        // Sudah final — tidak ada babak berikutnya untuk didorong.
        if ($this->round >= $totalRounds) {
            return;
        }

        $nextRound = $this->round + 1;
        $nextOrder = intdiv($this->round_order - 1, 2) + 1;
        $slot = $this->round_order % 2 === 1 ? 'team1_id' : 'team2_id';

        static::where('featured_event_id', $this->featured_event_id)
            ->where('round', $nextRound)
            ->where('round_order', $nextOrder)
            ->update([$slot => $this->winner_id]);
    }
}
