<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InsurancePolicy Model
 * Represents insurance policies for high-value items
 */
class InsurancePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'item_description',
        'coverage_amount',
        'premium',
        'type', // basic, premium, comprehensive
        'status',
        'policy_number',
        'start_date',
        'end_date',
        'claim_status',
        'claim_amount',
        'claim_reason',
        'claim_processed_at',
        'claim_documentation',
    ];

    protected $casts = [
        'coverage_amount' => 'decimal:2',
        'premium' => 'decimal:2',
        'claim_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'claim_processed_at' => 'datetime',
        'claim_documentation' => 'array',
    ];

    /**
     * Get the user who owns the insurance policy
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the associated order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the status options for policies
     */
    public static function getStatusOptions()
    {
        return [
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'claimed' => 'Claim Filed',
            'settled' => 'Claim Settled',
            'denied' => 'Claim Denied',
        ];
    }

    /**
     * Get the claim status options
     */
    public static function getClaimStatusOptions()
    {
        return [
            'pending' => 'Claim Pending Review',
            'approved' => 'Claim Approved',
            'denied' => 'Claim Denied',
            'settled' => 'Claim Settled',
            'processing' => 'Processing',
        ];
    }

    /**
     * Scope for active policies
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>=', now());
    }

    /**
     * Scope for expired policies
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope for policies with pending claims
     */
    public function scopePendingClaims($query)
    {
        return $query->where('claim_status', 'pending');
    }

    /**
     * Check if policy is currently active
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date >= now();
    }

    /**
     * Check if policy has expired
     */
    public function hasExpired()
    {
        return $this->end_date < now();
    }

    /**
     * Check if the policy has a pending claim
     */
    public function hasPendingClaim()
    {
        return !empty($this->claim_status) && $this->claim_status === 'pending';
    }

    /**
     * Calculate days remaining until expiry
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->end_date) {
            return now()->diffInDays($this->end_date, false); // false means positive if future
        }
        return 0;
    }
}