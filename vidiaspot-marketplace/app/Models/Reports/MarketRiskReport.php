<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketRiskReport extends Model
{
    protected $fillable = [
        'volatility_analysis',
        'liquidity_data',
        'counterparty_risk',
        'margin_call_data',
        'stop_loss_analysis',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'volatility_analysis' => 'array',
        'liquidity_data' => 'array',
        'counterparty_risk' => 'array',
        'margin_call_data' => 'array',
        'stop_loss_analysis' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}