<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTradingReport extends Model
{
    protected $fillable = [
        'volume_by_pair',
        'volume_by_currency',
        'transaction_counts',
        'fee_revenue',
        'settlement_status',
        'average_order_size',
        'total_volume',
        'total_transactions',
        'total_fees',
        'date',
        'metadata'
    ];

    protected $casts = [
        'volume_by_pair' => 'array',
        'volume_by_currency' => 'array',
        'transaction_counts' => 'array',
        'fee_revenue' => 'array',
        'settlement_status' => 'array',
        'average_order_size' => 'array',
        'metadata' => 'array',
        'date' => 'date',
        'total_volume' => 'decimal:8',
        'total_transactions' => 'integer',
        'total_fees' => 'decimal:8',
    ];
}