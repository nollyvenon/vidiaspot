<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictiveAnalyticsReport extends Model
{
    protected $fillable = [
        'market_trends',
        'user_growth_projections',
        'revenue_forecasts',
        'risk_assessments',
        'seasonal_patterns',
        'forecast_date',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'market_trends' => 'array',
        'user_growth_projections' => 'array',
        'revenue_forecasts' => 'array',
        'risk_assessments' => 'array',
        'seasonal_patterns' => 'array',
        'metadata' => 'array',
        'forecast_date' => 'date',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}