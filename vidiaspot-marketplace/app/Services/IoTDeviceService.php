<?php

namespace App\Services;

use App\Models\IoTDevice;
use App\Models\User;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class IoTDeviceService
{
    public function registerDevice($deviceId, $deviceData, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return IoTDevice::updateOrCreate(
            [
                'device_id' => $deviceId,
                'user_id' => $userId,
            ],
            [
                'name' => $deviceData['name'] ?? 'Smart Device',
                'device_type' => $deviceData['type'] ?? 'unknown',
                'brand' => $deviceData['brand'] ?? 'Generic',
                'model' => $deviceData['model'] ?? 'Model',
                'specs' => $deviceData['specs'] ?? [],
                'supported_protocols' => $deviceData['protocols'] ?? [],
                'firmware_version' => $deviceData['firmware'] ?? '1.0.0',
                'location' => $deviceData['location'] ?? null,
                'is_registered' => true,
                'registration_date' => now(),
            ]
        );
    }

    public function connectDevice($deviceId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $device = IoTDevice::where('device_id', $deviceId)
            ->where('user_id', $userId)
            ->first();

        if ($device) {
            $device->update([
                'is_connected' => true,
                'connection_status' => 'connected',
                'last_seen' => now(),
            ]);
        }

        return $device;
    }

    public function disconnectDevice($deviceId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $device = IoTDevice::where('device_id', $deviceId)
            ->where('user_id', $userId)
            ->first();

        if ($device) {
            $device->update([
                'is_connected' => false,
                'connection_status' => 'disconnected',
            ]);
        }

        return $device;
    }

    public function getConnectedDevices($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return IoTDevice::where('user_id', $userId)
            ->connected()
            ->forSmartHome()
            ->get();
    }

    public function getDeviceStatus($deviceId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return IoTDevice::where('device_id', $deviceId)
            ->where('user_id', $userId)
            ->select('id', 'name', 'device_type', 'status', 'connection_status', 'is_connected', 'last_seen')
            ->first();
    }

    public function linkToDeviceAd($deviceId, $adId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $device = IoTDevice::where('device_id', $deviceId)
            ->where('user_id', $userId)
            ->first();

        if ($device) {
            $device->update(['ad_id' => $adId]);
        }

        return $device;
    }

    public function getSmartHomeDevices($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return IoTDevice::where('user_id', $userId)
            ->forSmartHome()
            ->orderBy('name')
            ->get();
    }

    public function updateDeviceSettings($deviceId, $settings, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $device = IoTDevice::where('device_id', $deviceId)
            ->where('user_id', $userId)
            ->first();

        if ($device) {
            $device->update(['specs' => array_merge($device->specs ?? [], $settings)]);
        }

        return $device;
    }
}