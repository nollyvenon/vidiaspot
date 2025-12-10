<?php

namespace App\Models;

class CryptoTrade extends BaseModel
{
    protected $fillable = [
        'listing_id',
        'buyer_id',
        'seller_id',
        'trade_type',
        'crypto_currency',
        'fiat_currency',
        'crypto_amount',
        'fiat_amount',
        'exchange_rate',
        'payment_method',
        'status',
        'escrow_address',
        'trade_reference',
        'payment_details',
        'escrow_status',
        'payment_confirmed_at',
        'crypto_released_at',
        'trade_completed_at',
        'dispute_id',
        'dispute_resolved_at',
        'dispute_resolution',
        'buyer_rating',
        'seller_rating',
        'buyer_review',
        'seller_review',
        'security_level',
        'trade_limits',
        'verification_required',
        'metadata',
    ];

    protected $casts = [
        'crypto_amount' => 'decimal:8',
        'fiat_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:8',
        'payment_details' => 'array',
        'trade_limits' => 'array',
        'metadata' => 'array',
        'status' => 'string',
        'escrow_status' => 'string',
        'payment_confirmed_at' => 'datetime',
        'crypto_released_at' => 'datetime',
        'trade_completed_at' => 'datetime',
        'dispute_resolved_at' => 'datetime',
        'buyer_rating' => 'integer',
        'seller_rating' => 'integer',
        'security_level' => 'integer',
        'verification_required' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(CryptoListing::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function transactions()
    {
        return $this->hasMany(CryptoTradeTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_escrow', 'payment_confirmed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCryptoCurrency($query, $currency)
    {
        return $query->where('crypto_currency', $currency);
    }

    public function scopeByTradeType($query, $type)
    {
        return $query->where('trade_type', $type);
    }
}