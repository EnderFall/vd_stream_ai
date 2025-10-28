<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VARecommendation extends Model
{
    /**
     * Explicit table name to avoid Laravel pluralization producing "v_a_recommendations"
     */
    protected $table = 'va_recommendations';
    protected $fillable = [
        'user_id',
        'recommended_start',
        'reason',
        'confidence_score',
    ];

    protected $casts = [
        'recommended_start' => 'datetime',
        'confidence_score' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
