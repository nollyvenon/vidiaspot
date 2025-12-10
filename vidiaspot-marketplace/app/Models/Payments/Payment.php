<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'payment_gateway',
        'payment_method',
        'user_id',
        'ad_id',
        'subscription_id',
        'currency_code',
        'amount',
        'fees',
        'status',
        'metadata',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that made the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad associated with this payment.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the subscription associated with this payment.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope to get payments by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get payments by gateway.
     */
    public function scopeByGateway($query, $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }

    /**
     * Get all supported payment gateways.
     */
    public static function getSupportedGateways()
    {
        return [
            'paystack',
            'flutterwave',
            'stripe',
            'paypal',
            'mpesa',
            'sofort',
            'bank_transfer'
        ];
    }
}
