<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insurance extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'payment_transaction_id',
        'insurance_type',
        'provider',
        'policy_number',
        'premium_amount',
        'coverage_amount',
        'status',
        'risk_level',
        'effective_from',
        'effective_until',
        'exclusions',
        'beneficiaries',
        'claim_process',
        'last_claim_date',
        'total_claims',
        'documents',
        'terms_and_conditions',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_until' => 'date',
        'last_claim_date' => 'datetime',
        'premium_amount' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'beneficiaries' => 'array',
        'claim_process' => 'array',
        'documents' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }
}
