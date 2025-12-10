<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceMetricsReport extends Model
{
    protected $fillable = [
        'kpis',
        'roi_analysis',
        'efficiency_ratios',
        'benchmark_comparisons',
        'trend_analysis',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'kpis' => 'array',
        'roi_analysis' => 'array',
        'efficiency_ratios' => 'array',
        'benchmark_comparisons' => 'array',
        'trend_analysis' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}