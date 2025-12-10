<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'transaction_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'payment_gateway',
        'provider_reference',
        'payment_details',
        'processed_at',
        'confirmed_at',
        'expires_at',
        'callback_url',
        'metadata',
        'tax_region',
        'tax_rate',
        'tax_amount',
        'total_amount_with_tax',
        'tax_breakdown',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount_with_tax' => 'decimal:2',
        'payment_details' => 'array',
        'metadata' => 'array',
        'tax_breakdown' => 'array',
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