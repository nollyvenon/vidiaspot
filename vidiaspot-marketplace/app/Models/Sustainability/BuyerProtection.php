<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerProtection extends Model
{
    protected $fillable = [
        'user_id', // Who bought the protection
        'transaction_id', // The transaction this protects
        'transaction_type', // 'ad_purchase', 'food_order', 'insurance_purchase', 'service_booking'
        'transaction_reference', // Reference number of the original transaction
        'provider_id', // ID of the insurance/provider company
        'policy_number',
        'coverage_amount',
        'premium_amount',
        'status', // 'active', 'pending', 'expired', 'claimed', 'cancelled'
        'protection_type', // 'full_refund', 'partial_refund', 'replacement', 'repair'
        'coverage_terms', // Terms and conditions
        'exclusions', // What's not covered
        'claim_status', // 'no_claim', 'pending', 'approved', 'rejected'
        'claim_amount', // Amount claimed
        'claim_resolved_amount', // Amount actually paid
        'claim_date',
        'claim_resolution_date',
        'claim_details',
        'support_ticket_id', // Link to support ticket for the claim
        'renewal_date',
        'purchase_date',
        'expiry_date',
    ];

    protected $casts = [
        'coverage_amount' => 'decimal:2',
        'premium_amount' => 'decimal:2',
        'claim_amount' => 'decimal:2',
        'claim_resolved_amount' => 'decimal:2',
        'coverage_terms' => 'array',
        'exclusions' => 'array',
        'claim_details' => 'array',
        'purchase_date' => 'datetime',
        'expiry_date' => 'datetime',
        'claim_date' => 'datetime',
        'claim_resolution_date' => 'datetime',
        'renewal_date' => 'datetime',
    ];

    /**
     * Get the user who purchased the protection
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the provider for this protection
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(\App\Models\InsuranceProvider::class, 'provider_id');
    }

    /**
     * Scope to get active protections
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expiry_date', '>', now());
    }

    /**
     * Scope to get by transaction type
     */
    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope to get by claim status
     */
    public function scopeByClaimStatus($query, $status)
    {
        return $query->where('claim_status', $status);
    }

    /**
     * Check if this protection is still valid
     */
    public function isValid(): bool
    {
        return $this->status === 'active' && $this->expiry_date > now();
    }

    /**
     * Check if eligible for claim
     */
    public function isEligibleForClaim(): bool
    {
        return $this->isValid() && $this->claim_status === 'no_claim';
    }
}
