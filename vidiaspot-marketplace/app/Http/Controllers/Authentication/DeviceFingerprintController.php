<?php

namespace App\Http\Controllers;

use App\Services\DeviceFingerprintingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceFingerprintController extends Controller
{
    private DeviceFingerprintingService $deviceFingerprintingService;

    public function __construct()
    {
        $this->deviceFingerprintingService = new DeviceFingerprintingService();
    }

    /**
     * Get the current device fingerprint information.
     */
    public function getCurrentFingerprint(Request $request)
    {
        $fingerprint = $this->deviceFingerprintingService->generateAdvancedFingerprint($request);
        $user = Auth::user();

        return response()->json([
            'fingerprint' => $fingerprint['fingerprint'],
            'device_info' => $fingerprint['device_info'],
            'is_known_device' => $user ? $this->deviceFingerprintingService->isKnownDeviceForUser($user, $fingerprint['fingerprint']) : null,
            'timestamp' => $fingerprint['timestamp'],
        ]);
    }

    /**
     * Check if the current device is suspicious.
     */
    public function checkSuspiciousDevice(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $suspiciousCheck = $this->deviceFingerprintingService->isSuspiciousDevice($request, $user);

        return response()->json([
            'is_suspicious' => $suspiciousCheck['is_suspicious'],
            'reasons' => $suspiciousCheck['reasons'],
        ]);
    }

    /**
     * Get all devices associated with the authenticated user.
     */
    public function getUserDevices()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $devices = $this->deviceFingerprintingService->getUserDevices($user);

        return response()->json([
            'devices' => $devices,
            'count' => count($devices),
        ]);
    }

    /**
     * Remove a device from the user's trusted devices list.
     */
    public function removeDevice(Request $request)
    {
        // This would involve implementing a more complex system to manage trusted devices
        // For now, this is a placeholder implementation
        
        return response()->json([
            'message' => 'Device removal functionality would be implemented here',
        ]);
    }

    /**
     * Validate a device fingerprint token.
     */
    public function validateDeviceToken(Request $request)
    {
        $request->validate([
            'fingerprint' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $isKnown = $this->deviceFingerprintingService->isKnownDeviceForUser($user, $request->fingerprint);

        return response()->json([
            'is_known' => $isKnown,
            'trusted' => $isKnown, // Known devices are considered trusted
        ]);
    }
}