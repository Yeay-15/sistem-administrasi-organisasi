<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTeam extends Model
{
    protected $fillable = [
        'featured_event_id',
        'event_group_id',
        'name',
        'logo_path',
        'seed',
    ];

    public function featuredEvent(): BelongsTo
    {
        return $this->belongsTo(FeaturedEvent::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(EventGroup::class, 'event_group_id');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }
}
