<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoTradingPair extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency_id',
        'quote_currency_id',
        'pair_name',
        'symbol',
        'min_price',
        'max_price',
        'min_quantity',
        'max_quantity',
        'price_tick_size',
        'quantity_step_size',
        'status',
        'is_active',
    ];

    protected $casts = [
        'min_price' => 'decimal:8',
        'max_price' => 'decimal:8',
        'min_quantity' => 'decimal:8',
        'max_quantity' => 'decimal:8',
        'price_tick_size' => 'decimal:8',
        'quantity_step_size' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    // Relations
    public function baseCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class, 'base_currency_id');
    }

    public function quoteCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class, 'quote_currency_id');
    }

    public function tradingOrders()
    {
        return $this->hasMany(P2pCryptoTradingOrder::class);
    }

    public function tradeExecutions()
    {
        return $this->hasMany(P2pCryptoTradeExecution::class);
    }
}