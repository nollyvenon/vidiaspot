<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FraudDetection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ad_id',
        'payment_transaction_id',
        'type',
        'severity',
        'risk_score',
        'indicators',
        'behavioral_patterns',
        'suspicious_activities',
        'analysis_details',
        'status',
        'investigation_notes',
        'investigated_by_user_id',
        'investigated_at',
        'resolution_action',
        'resolution_details',
        'is_confirmed_fraud',
        'is_false_positive',
        'confidence_factors',
        'recommended_actions',
        'affected_resources',
        'detected_at',
        'resolved_at',
    ];

    protected $casts = [
        'risk_score' => 'decimal:2',
        'indicators' => 'array',
        'behavioral_patterns' => 'array',
        'suspicious_activities' => 'array',
        'confidence_factors' => 'array',
        'recommended_actions' => 'array',
        'affected_resources' => 'array',
        'is_confirmed_fraud' => 'boolean',
        'is_false_positive' => 'boolean',
        'investigated_at' => 'datetime',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
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

    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigated_by_user_id');
    }
}
