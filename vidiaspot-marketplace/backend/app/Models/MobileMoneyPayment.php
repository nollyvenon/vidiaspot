<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MobileMoneyPayment Model
 * Represents mobile money transactions
 */
class MobileMoneyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'provider', // mpesa, mtn, airtel, orange, tigo
        'mobile_number',
        'reason',
        'status', // pending, processing, completed, failed, cancelled
        'reference',
        'transaction_id',
        'payment_method',
        'fees',
        'external_reference',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the mobile money payment
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
            'pending' => 'Pending Confirmation',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
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
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', ['completed']);
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed']);
    }
}