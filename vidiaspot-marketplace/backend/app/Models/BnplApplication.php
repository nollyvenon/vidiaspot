<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BnplApplication Model
 * Represents buy-now-pay-later applications and approvals
 */
class BnplApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider', // klarna, afterpay, affirm, sezzle, zip
        'application_data',
        'credit_score',
        'monthly_income',
        'employment_type',
        'country',
        'status',
        'decision_reason',
        'credit_limit',
        'interest_rate',
        'payment_plan',
        'approval_date',
        'rejection_date',
        'documents',
        'external_application_id',
        'terms_accepted',
        'agreement_signed',
    ];

    protected $casts = [
        'application_data' => 'array',
        'documents' => 'array',
        'payment_plan' => 'array',
        'monthly_income' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'approval_date' => 'datetime',
        'rejection_date' => 'datetime',
        'terms_accepted' => 'boolean',
        'agreement_signed' => 'boolean',
    ];

    /**
     * Get the user who applied for BNPL
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
            'pending_review' => 'Pending Review',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'partially_approved' => 'Partially Approved',
            'rejected' => 'Rejected',
            'active' => 'Active Account',
            'inactive' => 'Inactive Account',
            'suspended' => 'Suspended',
            'closed' => 'Closed',
        ];
    }

    /**
     * Get the employment type options
     */
    public static function getEmploymentTypes()
    {
        return [
            'full_time' => 'Full-time Employee',
            'part_time' => 'Part-time Employee',
            'contractor' => 'Contractor/Freelancer',
            'government' => 'Government Employee',
            'retired' => 'Retired',
            'unemployed' => 'Unemployed',
            'student' => 'Student',
            'self_employed' => 'Self-Employed',
        ];
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending_review', 'under_review']);
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'active']);
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->whereIn('status', ['rejected', 'closed']);
    }

    /**
     * Get the provider options
     */
    public static function getProviderOptions()
    {
        return [
            'klarna' => 'Klarna',
            'afterpay' => 'Afterpay',
            'affirm' => 'Affirm',
            'sezzle' => 'Sezzle',
            'zip' => 'Zip Co',
            'quadpay' => 'Quadpay',
            'paybright' => 'PayBright',
            'laybuy' => 'Laybuy',
        ];
    }

    /**
     * Check if the application is approved
     */
    public function isApproved()
    {
        return in_array($this->status, ['approved', 'active', 'partially_approved']);
    }

    /**
     * Check if the application is pending review
     */
    public function isPending()
    {
        return in_array($this->status, ['pending_review', 'under_review']);
    }

    /**
     * Get the remaining credit available
     */
    public function getRemainingCreditAttribute()
    {
        if ($this->credit_limit && $this->status === 'approved') {
            // Calculate remaining credit based on existing orders
            // Implementation depends on how orders are tracked with BNPL
            return $this->credit_limit; // Simplified for now
        }
        return 0;
    }

    /**
     * Get payment plan details
     */
    public function getPaymentPlanDetailsAttribute()
    {
        if ($this->payment_plan) {
            return [
                'installments' => $this->payment_plan['installments'] ?? 4,
                'frequency' => $this->payment_plan['frequency'] ?? 'bi-weekly',
                'due_dates' => $this->payment_plan['due_dates'] ?? [],
                'amount_per_installment' => $this->payment_plan['amount_per_installment'] ?? 0,
            ];
        }
        return [];
    }
}