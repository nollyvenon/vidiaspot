<?php

namespace App\Models;

class CryptoTradeTransaction extends BaseModel
{
    protected $fillable = [
        'trade_id',
        'user_id',
        'transaction_type',
        'crypto_currency',
        'fiat_currency',
        'crypto_amount',
        'fiat_amount',
        'exchange_rate',
        'transaction_fee',
        'payment_method',
        'payment_details',
        'transaction_hash',
        'blockchain',
        'block_number',
        'from_address',
        'to_address',
        'status',
        'confirmed_at',
        'confirmation_blocks',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'crypto_amount' => 'decimal:8',
        'fiat_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:8',
        'transaction_fee' => 'decimal:8',
        'payment_details' => 'array',
        'confirmed_at' => 'datetime',
        'confirmation_blocks' => 'integer',
        'metadata' => 'array',
        'status' => 'string',
        'block_number' => 'integer',
    ];

    public function trade()
    {
        return $this->belongsTo(CryptoTrade::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCryptoCurrency($query, $currency)
    {
        return $query->where('crypto_currency', $currency);
    }
}