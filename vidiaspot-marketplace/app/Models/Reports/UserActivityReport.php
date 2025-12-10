<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityReport extends Model
{
    protected $fillable = [
        'dau_mau_data',
        'retention_data',
        'conversion_data',
        'geographic_data',
        'usage_patterns',
        'dau',
        'mau',
        'retention_rate',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'dau_mau_data' => 'array',
        'retention_data' => 'array',
        'conversion_data' => 'array',
        'geographic_data' => 'array',
        'usage_patterns' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'dau' => 'integer',
        'mau' => 'integer',
        'retention_rate' => 'decimal:4',
    ];
}