<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'visibility',
        'thumbnail_path',
        'videos_count',
        'total_duration',
    ];

    protected $casts = [
        'videos_count' => 'integer',
        'total_duration' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playlistVideos(): HasMany
    {
        return $this->hasMany(PlaylistVideo::class)->orderBy('position');
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'playlist_videos')
                    ->withPivot('position')
                    ->orderBy('playlist_videos.position');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        $seconds = $this->total_duration;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }

        return sprintf('%dm', $minutes);
    }

    public function getVideosCountAttribute()
    {
        return $this->videos()->count();
    }
}
