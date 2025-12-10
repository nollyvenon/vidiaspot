<?php

namespace App\Models;

class CryptoListing extends BaseModel
{
    protected $fillable = [
        'user_id',
        'crypto_currency',
        'fiat_currency',
        'trade_type',
        'price_per_unit',
        'min_trade_amount',
        'max_trade_amount',
        'available_amount',
        'reserved_amount',
        'payment_methods',
        'trade_limits',
        'trading_fee_percent',
        'trading_fee_fixed',
        'location',
        'location_radius',
        'trading_terms',
        'negotiable',
        'auto_accept',
        'auto_release_time_hours',
        'verification_level_required',
        'trade_security_level',
        'reputation_score',
        'trade_count',
        'completion_rate',
        'online_status',
        'status',
        'is_public',
        'featured',
        'pinned',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:8',
        'min_trade_amount' => 'decimal:2',
        'max_trade_amount' => 'decimal:2',
        'available_amount' => 'decimal:8',
        'reserved_amount' => 'decimal:8',
        'trading_fee_percent' => 'decimal:2',
        'trading_fee_fixed' => 'decimal:2',
        'location_radius' => 'decimal:2',
        'payment_methods' => 'array',
        'trade_limits' => 'array',
        'trading_terms' => 'array',
        'auto_release_time_hours' => 'integer',
        'verification_level_required' => 'integer',
        'trade_security_level' => 'integer',
        'reputation_score' => 'decimal:2',
        'trade_count' => 'integer',
        'completion_rate' => 'decimal:2',
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'negotiable' => 'boolean',
        'auto_accept' => 'boolean',
        'online_status' => 'boolean',
        'is_public' => 'boolean',
        'featured' => 'boolean',
        'pinned' => 'boolean',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trades()
    {
        return $this->hasMany(CryptoTrade::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('is_public', true)
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeByTradeType($query, $type)
    {
        return $query->where('trade_type', $type);
    }

    public function scopeByCryptoCurrency($query, $currency)
    {
        return $query->where('crypto_currency', $currency);
    }

    public function scopeByFiatCurrency($query, $currency)
    {
        return $query->where('fiat_currency', $currency);
    }

    public function scopeBuyOrders($query)
    {
        return $query->where('trade_type', 'buy');
    }

    public function scopeSellOrders($query)
    {
        return $query->where('trade_type', 'sell');
    }

    public function scopeNearLocation($query, $lat, $lng, $radiusKm = 50)
    {
        // This would typically use a geographic query
        // For now, we'll return all listings in the same country
        return $query->whereNotNull('location');
    }
}