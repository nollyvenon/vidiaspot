<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostTimingSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ad_id',
        'category_id',
        'location_id',
        'best_day_of_week',
        'best_hour',
        'optimal_score',
        'factors',
        'historical_data',
        'seasonal_trend',
        'expected_views',
        'expected_responses',
        'competition_level',
        'reasoning',
        'alternative_times',
        'valid_from',
        'valid_until',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'optimal_score' => 'decimal:2',
        'factors' => 'array',
        'historical_data' => 'array',
        'expected_views' => 'integer',
        'expected_responses' => 'integer',
        'competition_level' => 'integer',
        'alternative_times' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'best_hour' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(City::class, 'location_id');
    }
}
