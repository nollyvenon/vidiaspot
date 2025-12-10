<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueRecognitionReport extends Model
{
    protected $fillable = [
        'trading_fee_revenue',
        'unearned_revenue',
        'bad_debt',
        'revenue_by_geographic',
        'deferred_revenue',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'trading_fee_revenue' => 'array',
        'unearned_revenue' => 'array',
        'bad_debt' => 'array',
        'revenue_by_geographic' => 'array',
        'deferred_revenue' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}