<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeStatementReport extends Model
{
    protected $fillable = [
        'revenue',
        'cost_of_goods_sold',
        'operating_expenses',
        'other_income_expenses',
        'total_revenue',
        'total_cost_of_goods_sold',
        'total_operating_expenses',
        'gross_profit',
        'operating_income',
        'net_income',
        'period_start',
        'period_end',
        'revenue_by_currency',
        'metadata'
    ];

    protected $casts = [
        'revenue' => 'array',
        'cost_of_goods_sold' => 'array',
        'operating_expenses' => 'array',
        'other_income_expenses' => 'array',
        'revenue_by_currency' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_revenue' => 'decimal:8',
        'total_cost_of_goods_sold' => 'decimal:8',
        'total_operating_expenses' => 'decimal:8',
        'gross_profit' => 'decimal:8',
        'operating_income' => 'decimal:8',
        'net_income' => 'decimal:8',
    ];
}