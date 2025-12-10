<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoTradingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trading_pair_id',
        'order_type',
        'side',
        'quantity',
        'executed_quantity',
        'price',
        'stop_price',
        'avg_price',
        'status',
        'time_in_force',
        'good_till_date',
        'fee',
        'fee_currency',
        'notes',
        'metadata',
        'is_oco_group_member',
        'oco_group_id',
        'is_grid_member',
        'grid_group_id',
        'is_grid_protection',
        'post_only',
        'reduce_only',
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'executed_quantity' => 'decimal:8',
        'price' => 'decimal:8',
        'stop_price' => 'decimal:8',
        'avg_price' => 'decimal:8',
        'fee' => 'decimal:8',
        'good_till_date' => 'datetime',
        'metadata' => 'array',
        'is_oco_group_member' => 'boolean',
        'is_grid_member' => 'boolean',
        'is_grid_protection' => 'boolean',
        'post_only' => 'boolean',
        'reduce_only' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradingPair()
    {
        return $this->belongsTo(P2pCryptoTradingPair::class);
    }

    public function tradeExecutions()
    {
        return $this->hasMany(P2pCryptoTradeExecution::class, 'trading_order_id');
    }

    public function makerExecutions()
    {
        return $this->hasMany(P2pCryptoTradeExecution::class, 'maker_order_id');
    }

    public function takerExecutions()
    {
        return $this->hasMany(P2pCryptoTradeExecution::class, 'taker_order_id');
    }
}