<?php

namespace App\Models;

class IoTDevice extends BaseModel
{
    protected $fillable = [
        'name',
        'device_id',
        'user_id',
        'ad_id',
        'device_type',
        'brand',
        'model',
        'status',
        'connection_status',
        'last_seen',
        'location',
        'specs',
        'supported_protocols',
        'firmware_version',
        'is_connected',
        'is_registered',
        'registration_date',
    ];

    protected $casts = [
        'specs' => 'array',
        'supported_protocols' => 'array',
        'last_seen' => 'datetime',
        'registration_date' => 'datetime',
        'is_connected' => 'boolean',
        'is_registered' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function scopeConnected($query)
    {
        return $query->where('is_connected', true);
    }

    public function scopeForSmartHome($query)
    {
        return $query->whereIn('device_type', ['lighting', 'thermostat', 'security', 'entertainment', 'appliances', 'sensors']);
    }
}