<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ad_id',
        'promotion_id',
        'order_reference',
        'total_amount',
        'currency',
        'status',
        'duration_days',
        'start_date',
        'end_date',
        'features_applied',
        'promotion_settings',
        'activated_at',
        'deactivated_at',
        'activity_log',
        'auto_renew',
        'payment_transaction_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'duration_days' => 'integer',
        'features_applied' => 'array',
        'promotion_settings' => 'array',
        'activity_log' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'auto_renew' => 'boolean',
        'status' => 'string',
    ];

    /**
     * Get the user who ordered the promotion
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad associated with this promotion order
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the promotion that was ordered
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Get the payment transaction associated with this order
     */
    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    /**
     * Check if the promotion is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               now()->between($this->start_date, $this->end_date);
    }

    /**
     * Check if the promotion has expired
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->end_date);
    }

    /**
     * Scope to get active promotion orders
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    /**
     * Scope to get expired promotion orders
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope to get pending promotion orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
