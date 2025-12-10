<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlowReport extends Model
{
    protected $fillable = [
        'operating_cash_flow',
        'investing_cash_flow',
        'financing_cash_flow',
        'free_cash_flow',
        'net_operating_cash_flow',
        'net_investing_cash_flow',
        'net_financing_cash_flow',
        'net_free_cash_flow',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'operating_cash_flow' => 'array',
        'investing_cash_flow' => 'array',
        'financing_cash_flow' => 'array',
        'free_cash_flow' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'net_operating_cash_flow' => 'decimal:8',
        'net_investing_cash_flow' => 'decimal:8',
        'net_financing_cash_flow' => 'decimal:8',
        'net_free_cash_flow' => 'decimal:8',
    ];
}