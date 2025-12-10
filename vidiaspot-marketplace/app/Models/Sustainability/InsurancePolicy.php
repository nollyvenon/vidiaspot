<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'policy_number',
        'provider',
        'provider_id',
        'coverage_type',
        'policy_title',
        'description',
        'premium_amount',
        'coverage_amount',
        'status',
        'risk_level',
        'effective_from',
        'effective_until',
        'billing_cycle',
        'coverage_details',
        'exclusions',
        'claim_requirements',
        'beneficiaries',
        'documents',
        'terms_and_conditions',
        'custom_fields',
        'claimed_at',
        'expires_at',
        'insurance_category', // life, health, motor, travel, home, term
        'insured_value',
        'deductible_amount',
        'payment_frequency',
        'agent_id',
        'commission_rate',
        'commission_amount',
        'renewal_reminder_sent',
        'next_renewal_date',
        'policy_type', // individual, family, business
        'coverage_area',
        'network_hospitals', // for health insurance
        'zero_depreciation', // for motor insurance
        'ncb_protector', // for motor insurance
        'policy_documents', // store policy document paths
        'claim_status',
        'claim_amount',
        'claim_date',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_until' => 'date',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'next_renewal_date' => 'date',
        'claim_date' => 'date',
        'premium_amount' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'insured_value' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'claim_amount' => 'decimal:2',
        'coverage_details' => 'array',
        'exclusions' => 'array',
        'claim_requirements' => 'array',
        'beneficiaries' => 'array',
        'documents' => 'array',
        'custom_fields' => 'array',
        'network_hospitals' => 'array',
        'policy_documents' => 'array',
        'renewal_reminder_sent' => 'boolean',
        'zero_depreciation' => 'boolean',
        'ncb_protector' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function providerInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InsuranceProvider::class, 'provider_id');
    }
}
