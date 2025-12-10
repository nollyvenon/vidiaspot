<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_per_day',
        'duration_days',
        'features',
        'is_active',
        'is_featured',
        'display_order',
        'promotion_type',
        'target_audience',
        'placement_positions',
        'max_allowed_per_ad',
        'requirements',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'duration_days' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'display_order' => 'integer',
        'placement_positions' => 'array',
        'max_allowed_per_ad' => 'integer',
        'requirements' => 'array',
    ];

    /**
     * Get the promotion orders for this promotion
     */
    public function promotionOrders(): HasMany
    {
        return $this->hasMany(PromotionOrder::class);
    }

    /**
     * Scope to get active promotions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured promotions
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get promotions by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('promotion_type', $type);
    }
}
