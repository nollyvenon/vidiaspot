<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency_code',
        'billing_cycle',
        'duration_days',
        'features',
        'is_active',
        'is_featured',
        'ad_limit',
        'featured_ads_limit',
        'has_priority_support',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'has_priority_support' => 'boolean',
        'duration_days' => 'integer',
        'ad_limit' => 'integer',
        'featured_ads_limit' => 'integer',
        'features' => 'array',
    ];

    /**
     * Get the payments for this subscription.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'subscription_id');
    }

    /**
     * Scope to get active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured subscriptions.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
