<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'video_id',
        'parent_id',
        'content',
        'is_approved',
        'likes_count',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'likes_count' => 'integer',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    // Accessors
    public function getLikeCountAttribute()
    {
        return $this->likes()->where('type', 'like')->count();
    }

    public function getDislikeCountAttribute()
    {
        return $this->likes()->where('type', 'dislike')->count();
    }
}
