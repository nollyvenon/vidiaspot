<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SplitPayment extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'payment_transaction_id',
        'total_amount',
        'amount_paid',
        'amount_remaining',
        'status',
        'title',
        'description',
        'participant_count',
        'expires_at',
        'participants',
        'payment_details',
        'settings',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
        'participants' => 'array',
        'payment_details' => 'array',
        'settings' => 'array',
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
