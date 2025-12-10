<?php

namespace App\Models;

class Drone extends BaseModel
{
    protected $fillable = [
        'name',
        'serial_number',
        'model',
        'manufacturer',
        'current_location_lat',
        'current_location_lng',
        'battery_level',
        'status',
        'is_available',
        'max_payload',
        'max_flight_time',
        'max_altitude',
        'max_speed',
        'last_maintenance_date',
        'next_maintenance_date',
        'flight_hours',
        'maintenance_hours',
        'specs',
        'assigned_courier_partner_id',
        'current_mission_id',
    ];

    protected $casts = [
        'specs' => 'array',
        'current_location_lat' => 'decimal:8',
        'current_location_lng' => 'decimal:8',
        'battery_level' => 'decimal:2',
        'max_payload' => 'decimal:2',
        'max_flight_time' => 'decimal:2',
        'max_altitude' => 'decimal:2',
        'max_speed' => 'decimal:2',
        'is_available' => 'boolean',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'flight_hours' => 'decimal:2',
        'maintenance_hours' => 'decimal:2',
    ];

    public function assignedCourierPartner()
    {
        return $this->belongsTo(\App\Models\Logistics\CourierPartner::class);
    }

    public function currentMission()
    {
        return $this->belongsTo(DroneMission::class, 'current_mission_id');
    }

    public function missions()
    {
        return $this->hasMany(DroneMission::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->where('status', 'active');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}