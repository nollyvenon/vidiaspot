<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyNowPayLater extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'payment_transaction_id',
        'provider',
        'total_amount',
        'down_payment',
        'installment_count',
        'installment_amount',
        'frequency',
        'status',
        'first_payment_date',
        'last_payment_date',
        'next_payment_date',
        'completion_date',
        'provider_details',
        'payment_schedule',
        'checks',
        'apr_rate',
        'agreement_url',
    ];

    protected $casts = [
        'first_payment_date' => 'date',
        'last_payment_date' => 'datetime',
        'next_payment_date' => 'datetime',
        'completion_date' => 'datetime',
        'provider_details' => 'array',
        'payment_schedule' => 'array',
        'checks' => 'array',
        'total_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'apr_rate' => 'decimal:2',
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
