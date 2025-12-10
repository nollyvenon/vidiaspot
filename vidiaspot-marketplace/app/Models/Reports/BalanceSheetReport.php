<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceSheetReport extends Model
{
    protected $fillable = [
        'report_type',
        'assets',
        'liabilities',
        'equity',
        'working_capital',
        'fixed_assets',
        'total_assets',
        'total_liabilities',
        'total_equity',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'assets' => 'array',
        'liabilities' => 'array',
        'equity' => 'array',
        'working_capital' => 'array',
        'fixed_assets' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_assets' => 'decimal:8',
        'total_liabilities' => 'decimal:8',
        'total_equity' => 'decimal:8',
    ];
}