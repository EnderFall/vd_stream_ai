<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'schedule_id',
        'type',
        'category',
        'amount',
        'date',
        'tags',
        'receipt_url',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'tags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
