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
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_until' => 'date',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'premium_amount' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'coverage_details' => 'array',
        'exclusions' => 'array',
        'claim_requirements' => 'array',
        'beneficiaries' => 'array',
        'documents' => 'array',
        'custom_fields' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }
}
