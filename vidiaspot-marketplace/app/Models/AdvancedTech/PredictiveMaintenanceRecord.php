<?php

namespace App\Models;

class PredictiveMaintenanceRecord extends BaseModel
{
    protected $fillable = [
        'device_id',
        'iot_device_id',
        'ad_id',
        'user_id',
        'maintenance_type',
        'predicted_failure_date',
        'confidence_level',
        'maintenance_priority',
        'status',
        'symptoms',
        'detected_anomalies',
        'maintenance_suggestions',
        'maintenance_cost_estimate',
        'maintenance_duration_estimate',
        'recommended_parts',
        'sensor_data',
        'ai_analysis',
        'maintenance_schedule_date',
        'actual_maintenance_date',
        'maintenance_performed_by',
        'maintenance_cost_actual',
        'resolved',
        'feedback',
        'metadata',
    ];

    protected $casts = [
        'symptoms' => 'array',
        'detected_anomalies' => 'array',
        'maintenance_suggestions' => 'array',
        'recommended_parts' => 'array',
        'sensor_data' => 'array',
        'ai_analysis' => 'array',
        'predicted_failure_date' => 'datetime',
        'maintenance_schedule_date' => 'datetime',
        'actual_maintenance_date' => 'datetime',
        'confidence_level' => 'decimal:2',
        'maintenance_cost_estimate' => 'decimal:2',
        'maintenance_duration_estimate' => 'decimal:2',
        'maintenance_cost_actual' => 'decimal:2',
        'maintenance_priority' => 'integer',
        'resolved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function iotDevice()
    {
        return $this->belongsTo(IoTDevice::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('maintenance_priority', '>', 5);
    }

    public function scopeForFailureDate($query, $date)
    {
        return $query->where('predicted_failure_date', '<=', $date);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }
}