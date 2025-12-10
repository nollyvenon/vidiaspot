<?php

namespace App\Models;

class DroneMission extends BaseModel
{
    protected $fillable = [
        'drone_id',
        'order_id',
        'delivery_address_id',
        'mission_type',
        'status',
        'estimated_departure_time',
        'estimated_arrival_time',
        'actual_departure_time',
        'actual_arrival_time',
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'distance',
        'estimated_duration',
        'actual_duration',
        'weather_conditions',
        'battery_at_departure',
        'battery_at_arrival',
        'payload_weight',
        'waypoints',
        'tracking_data',
        'completed_at',
    ];

    protected $casts = [
        'origin_lat' => 'decimal:8',
        'origin_lng' => 'decimal:8',
        'destination_lat' => 'decimal:8',
        'destination_lng' => 'decimal:8',
        'distance' => 'decimal:2',
        'estimated_duration' => 'decimal:2',
        'actual_duration' => 'decimal:2',
        'battery_at_departure' => 'decimal:2',
        'battery_at_arrival' => 'decimal:2',
        'payload_weight' => 'decimal:2',
        'waypoints' => 'array',
        'tracking_data' => 'array',
        'estimated_departure_time' => 'datetime',
        'estimated_arrival_time' => 'datetime',
        'actual_departure_time' => 'datetime',
        'actual_arrival_time' => 'datetime',
        'completed_at' => 'datetime',
        'weather_conditions' => 'array',
    ];

    public function drone()
    {
        return $this->belongsTo(Drone::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(Address::class, 'delivery_address_id'); // assuming there's an Address model
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress', 'completed']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}