<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CryptoPayment Model
 * Represents cryptocurrency transactions
 */
class CryptoPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'currency', // btc, eth, usdc, etc.
        'recipient_address',
        'wallet_address',
        'tx_hash',
        'note',
        'status',
        'reference',
        'fees',
        'blockchain_network',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the crypto payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status options
     */
    public static function getStatusOptions()
    {
        return [
            'pending',
            'processing',
            'completed',
            'failed',
            'expired',
            'refunded',
            'partially_paid',
        ];
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}