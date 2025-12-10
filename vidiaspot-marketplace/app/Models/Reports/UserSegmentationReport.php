<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSegmentationReport extends Model
{
    protected $fillable = [
        'tier_based_data',
        'vip_user_data',
        'regional_user_data',
        'new_returning_data',
        'power_user_data',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'tier_based_data' => 'array',
        'vip_user_data' => 'array',
        'regional_user_data' => 'array',
        'new_returning_data' => 'array',
        'power_user_data' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}