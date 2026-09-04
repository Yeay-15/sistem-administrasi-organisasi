<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventUpdate extends Model
{
    protected $fillable = [
        'featured_event_id',
        'title',
        'body',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function featuredEvent(): BelongsTo
    {
        return $this->belongsTo(FeaturedEvent::class);
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }
}
