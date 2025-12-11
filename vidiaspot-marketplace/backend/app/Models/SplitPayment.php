<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SplitPayment Model
 * Represents split payment transactions for group purchases
 */
class SplitPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'total_amount',
        'participants',
        'note',
        'status',
        'reference',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'participants' => 'array', // JSON array of participants with email and amount
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user who initiated the split payment
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    /**
     * Get the status options
     */
    public static function getStatusOptions()
    {
        return [
            'awaiting_payment' => 'Awaiting Payment',
            'partially_paid' => 'Partially Paid',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
        ];
    }

    /**
     * Scope for active split payments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['awaiting_payment', 'partially_paid']);
    }

    /**
     * Scope for completed split payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if the split payment has expired
     */
    public function isExpired()
    {
        if ($this->expires_at) {
            return now()->gte($this->expires_at);
        }
        return false;
    }

    /**
     * Get the total amount paid so far
     */
    public function getTotalPaidAttribute()
    {
        $paidAmount = 0;
        foreach ($this->participants as $participant) {
            if (isset($participant['paid']) && $participant['paid']) {
                $paidAmount += floatval($participant['amount']);
            }
        }
        return $paidAmount;
    }

    /**
     * Get the remaining amount to be paid
     */
    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->total_paid;
    }

    /**
     * Check if the payment is fully paid
     */
    public function isFullyPaid()
    {
        return $this->total_paid >= $this->total_amount;
    }
}