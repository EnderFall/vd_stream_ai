<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'videos_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'videos_count' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Accessors
    public function getVideosCountAttribute()
    {
        return $this->videos()->count();
    }
}
