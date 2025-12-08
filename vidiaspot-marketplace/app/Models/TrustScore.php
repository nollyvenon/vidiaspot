<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustScore extends Model
{
    protected $fillable = [
        'user_id',
        'trust_score', // Overall trust score (0-100)
        'trust_metrics', // Detailed metrics like {transaction_success_rate: 95, dispute_rate: 2, complaint_count: 1}
        'verification_level', // 'basic', 'verified', 'trusted', 'elite'
        'performance_indicators', // {seller_rating: 4.5, buyer_feedback: 4.7, response_time_avg: 2.5}
        'activity_history', // Recent activity patterns
        'suspicious_activity_count',
        'dispute_count',
        'complaint_count',
        'positive_interactions',
        'negative_interactions',
        'account_age_months',
        'total_transactions',
        'total_revenue',
        'fraud_indicators', // Risk factors detected
        'protection_eligibility', // Eligibility for buyer protection
        'insurance_eligibility', // Eligibility for insurance
        'background_check_status', // 'pending', 'verified', 'flagged'
        'background_check_details',
        'last_updated',
        'next_review_date',
    ];

    protected $casts = [
        'trust_score' => 'decimal:2',
        'trust_metrics' => 'array',
        'performance_indicators' => 'array',
        'activity_history' => 'array',
        'fraud_indicators' => 'array',
        'background_check_details' => 'array',
        'suspicious_activity_count' => 'integer',
        'dispute_count' => 'integer',
        'complaint_count' => 'integer',
        'positive_interactions' => 'integer',
        'negative_interactions' => 'integer',
        'account_age_months' => 'integer',
        'total_transactions' => 'integer',
        'total_revenue' => 'decimal:2',
        'last_updated' => 'datetime',
        'next_review_date' => 'datetime',
    ];

    /**
     * Get the user that this trust score belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the seller performance dashboard
     */
    public function getSellerPerformance()
    {
        if (isset($this->performance_indicators['seller_rating'])) {
            return [
                'rating' => $this->performance_indicators['seller_rating'],
                'feedback_count' => $this->positive_interactions + $this->negative_interactions,
                'response_rate' => $this->getAverageResponseRate(),
                'completion_rate' => $this->getCompletionRate(),
            ];
        }
        return null;
    }

    /**
     * Get average response rate
     */
    private function getAverageResponseRate()
    {
        // Placeholder: actual implementation would depend on message data
        return 90; // Example value
    }

    /**
     * Get completion rate
     */
    private function getCompletionRate()
    {
        if ($this->total_transactions > 0) {
            return ((($this->total_transactions - $this->dispute_count) / $this->total_transactions) * 100);
        }
        return 0;
    }

    /**
     * Check if user is eligible for buyer protection
     */
    public function isEligibleForBuyerProtection(): bool
    {
        return $this->protection_eligibility && $this->trust_score >= 70;
    }

    /**
     * Check if user is eligible for transaction insurance
     */
    public function isEligibleForInsurance(): bool
    {
        return $this->insurance_eligibility && $this->trust_score >= 65;
    }

    /**
     * Check if user passed background check
     */
    public function passedBackgroundCheck(): bool
    {
        return $this->background_check_status === 'verified';
    }
}
