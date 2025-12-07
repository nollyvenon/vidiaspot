<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedAd extends Model
{
    protected $fillable = [
        'ad_id',
        'user_id',
        'payment_id',
        'type',
        'cost',
        'currency_code',
        'starts_at',
        'expires_at',
        'status',
        'settings',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Get the ad associated with this featured ad.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the user who featured the ad.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment for this featured ad.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope to get active featured ads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope to get expired featured ads.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
