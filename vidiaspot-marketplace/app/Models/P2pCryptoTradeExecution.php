<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoTradeExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'trading_order_id',
        'maker_order_id',
        'taker_order_id',
        'trading_pair_id',
        'side',
        'quantity',
        'price',
        'fee',
        'fee_currency',
        'fee_payer',
        'executed_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'price' => 'decimal:8',
        'fee' => 'decimal:8',
        'executed_at' => 'datetime',
    ];

    // Relations
    public function tradingOrder()
    {
        return $this->belongsTo(P2pCryptoTradingOrder::class);
    }

    public function makerOrder()
    {
        return $this->belongsTo(P2pCryptoTradingOrder::class, 'maker_order_id');
    }

    public function takerOrder()
    {
        return $this->belongsTo(P2pCryptoTradingOrder::class, 'taker_order_id');
    }

    public function tradingPair()
    {
        return $this->belongsTo(P2pCryptoTradingPair::class);
    }
}