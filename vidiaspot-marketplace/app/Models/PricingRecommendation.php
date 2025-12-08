<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricingRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'category_id',
        'location_id',
        'current_price',
        'recommended_price',
        'market_average',
        'min_price',
        'max_price',
        'confidence_level',
        'pricing_strategy',
        'analysis_data',
        'market_trends',
        'reasoning',
        'is_optimal',
        'expires_at',
        'generated_at',
    ];

    protected $casts = [
        'current_price' => 'decimal:2',
        'recommended_price' => 'decimal:2',
        'market_average' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'confidence_level' => 'decimal:2',
        'analysis_data' => 'array',
        'market_trends' => 'array',
        'is_optimal' => 'boolean',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

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