<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemPerformanceReport extends Model
{
    protected $fillable = [
        'uptime_data',
        'response_times',
        'error_rates',
        'load_balancing',
        'backup_recovery',
        'uptime_percentage',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'uptime_data' => 'array',
        'response_times' => 'array',
        'error_rates' => 'array',
        'load_balancing' => 'array',
        'backup_recovery' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'uptime_percentage' => 'decimal:4',
    ];
}