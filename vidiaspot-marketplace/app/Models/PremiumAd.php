<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumAd extends Model
{
    protected $fillable = [
        'ad_id',
        'user_id',
        'payment_id',
        'campaign_name',
        'budget',
        'currency_code',
        'ad_type',
        'targeting_settings',
        'impressions_goal',
        'clicks_goal',
        'start_date',
        'end_date',
        'status',
        'daily_budget',
        'placement_settings',
        'impressions_count',
        'clicks_count',
        'spent_amount',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'daily_budget' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'impressions_goal' => 'integer',
        'clicks_goal' => 'integer',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
        'spent_amount' => 'decimal:2',
        'targeting_settings' => 'array',
        'placement_settings' => 'array',
    ];

    /**
     * Get the ad associated with this premium ad.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the user who created this premium ad.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment associated with this premium ad.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope to get active premium ads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>', now());
    }

    /**
     * Scope to get by ad type.
     */
    public function scopeByAdType($query, $adType)
    {
        return $query->where('ad_type', $adType);
    }
}
