<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InAppPurchase extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'ad_id',
        'vendor_id',
        'purchase_type',
        'item_type',
        'item_id',
        'package_type',
        'amount',
        'currency_code',
        'purchase_details',
        'payment_gateway',
        'status',
        'completed_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'ad_id' => 'integer',
        'vendor_id' => 'integer',
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'purchase_details' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the user who made the purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad associated with this purchase.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the vendor associated with this purchase.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Scope to get purchases by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('purchase_type', $type);
    }

    /**
     * Scope to get completed purchases.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get by payment gateway.
     */
    public function scopeByGateway($query, $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }
}
