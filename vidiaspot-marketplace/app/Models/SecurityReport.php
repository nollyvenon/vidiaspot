<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityReport extends Model
{
    protected $fillable = [
        'suspicious_activities',
        'fraud_detection',
        'kyc_aml_compliance',
        'system_security',
        'dispute_resolutions',
        'suspicious_activity_count',
        'fraud_detection_count',
        'dispute_count',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'suspicious_activities' => 'array',
        'fraud_detection' => 'array',
        'kyc_aml_compliance' => 'array',
        'system_security' => 'array',
        'dispute_resolutions' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'suspicious_activity_count' => 'integer',
        'fraud_detection_count' => 'integer',
        'dispute_count' => 'integer',
    ];
}