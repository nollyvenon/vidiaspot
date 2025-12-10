<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptocurrencyPayment extends Model
{
    protected $fillable = [
        'user_id',
        'payment_transaction_id',
        'crypto_currency',
        'wallet_address',
        'amount_crypto',
        'amount_ngn',
        'exchange_rate',
        'transaction_hash',
        'status',
        'network',
        'confirmed_at',
        'expires_at',
        'raw_data',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount_crypto' => 'decimal:8',
        'amount_ngn' => 'decimal:2',
        'exchange_rate' => 'decimal:8',
        'raw_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }
}
