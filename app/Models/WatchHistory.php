<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
        'watched_seconds',
        'total_seconds',
        'completed',
        'last_watched_at',
    ];

    protected $casts = [
        'watched_seconds' => 'integer',
        'total_seconds' => 'integer',
        'completed' => 'boolean',
        'last_watched_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        if ($this->total_seconds == 0) {
            return 0;
        }

        return round(($this->watched_seconds / $this->total_seconds) * 100, 2);
    }
}
