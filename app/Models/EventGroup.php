<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventGroup extends Model
{
    protected $fillable = [
        'featured_event_id',
        'name',
        'order',
    ];

    public function featuredEvent(): BelongsTo
    {
        return $this->belongsTo(FeaturedEvent::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(EventTeam::class)->orderBy('name');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(EventMatch::class)->orderBy('round_order');
    }

    /**
     * Klasemen grup, dihitung langsung dari pertandingan yang sudah
     * berstatus "Selesai" dengan skor lengkap — tidak disimpan sebagai
     * kolom terpisah supaya tidak pernah tidak-sinkron dengan hasil
     * pertandingan yang sebenarnya. Diurutkan: poin, lalu selisih gol,
     * lalu jumlah gol memasukkan (aturan klasemen standar).
     */
    public function standings()
    {
        $stats = [];
        foreach ($this->teams as $team) {
            $stats[$team->id] = [
                'team' => $team,
                'played' => 0, 'won' => 0, 'draw' => 0, 'lost' => 0,
                'gf' => 0, 'ga' => 0,
            ];
        }

        $finishedMatches = $this->matches()
            ->where('status', 'finished')
            ->whereNotNull('team1_score')
            ->whereNotNull('team2_score')
            ->get();

        foreach ($finishedMatches as $match) {
            $this->applyMatchToStats($stats, $match->team1_id, $match->team1_score, $match->team2_score);
            $this->applyMatchToStats($stats, $match->team2_id, $match->team2_score, $match->team1_score);
        }

        $rows = array_map(function ($row) {
            $row['points'] = $row['won'] * 3 + $row['draw'];
            $row['gd'] = $row['gf'] - $row['ga'];
            return $row;
        }, array_values($stats));

        usort($rows, fn ($a, $b) => [$b['points'], $b['gd'], $b['gf']] <=> [$a['points'], $a['gd'], $a['gf']]);

        return collect($rows);
    }

    private function applyMatchToStats(array &$stats, ?int $teamId, ?int $scoredFor, ?int $scoredAgainst): void
    {
        if (! $teamId || ! isset($stats[$teamId])) {
            return;
        }

        $stats[$teamId]['played']++;
        $stats[$teamId]['gf'] += $scoredFor;
        $stats[$teamId]['ga'] += $scoredAgainst;

        if ($scoredFor > $scoredAgainst) {
            $stats[$teamId]['won']++;
        } elseif ($scoredFor < $scoredAgainst) {
            $stats[$teamId]['lost']++;
        } else {
            $stats[$teamId]['draw']++;
        }
    }
}
