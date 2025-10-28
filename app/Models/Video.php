<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Video extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'video_path',
        'thumbnail_path',
        'duration',
        'status',
        'visibility',
        'tags',
        'views_count',
        'likes_count',
        'comments_count',
        'category_id',
    ];

    protected $casts = [
        'tags' => 'array',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'duration' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function playlists(): HasMany
    {
        return $this->hasMany(PlaylistVideo::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        $seconds = $this->duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getLikeCountAttribute()
    {
        return $this->likes()->where('type', 'like')->count();
    }

    public function getDislikeCountAttribute()
    {
        return $this->likes()->where('type', 'dislike')->count();
    }
}
