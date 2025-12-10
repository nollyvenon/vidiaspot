<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MobileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MobileController extends Controller
{
    protected MobileService $mobileService;

    public function __construct(MobileService $mobileService)
    {
        $this->mobileService = $mobileService;
    }

    /**
     * Register a mobile device for push notifications
     */
    public function registerDevice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'device_token' => 'required|string',
            'platform' => 'required|in:ios,android,web',
            'app_version' => 'nullable|string',
            'os_version' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'model' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $deviceInfo = [
            'platform' => $request->platform,
            'os_version' => $request->os_version,
            'app_version' => $request->app_version,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
        ];

        $device = $this->mobileService->registerMobileDevice(
            auth()->id(),
            $request->device_id,
            $request->device_token,
            $deviceInfo
        );

        return response()->json([
            'success' => true,
            'data' => $device,
            'message' => 'Device registered successfully'
        ]);
    }

    /**
     * Handle biometric authentication
     */
    public function biometricAuth(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'biometric_data' => 'required|array',
            'biometric_data.type' => 'required|in:face_id,touch_id,fingerprint,iris_scan',
            'biometric_data.template' => 'required|string',
            'biometric_data.device' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->mobileService->handleBiometricAuth(
            auth()->id(),
            $request->biometric_data
        );

        return response()->json($result);
    }

    /**
     * Process QR code scan
     */
    public function processQRCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|array',
            'qr_data.transaction_type' => 'required|in:payment_request,product_info,user_transfer,location_checkin',
            'qr_data.data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->mobileService->processQRCodeTransaction(
            auth()->id(),
            $request->qr_data
        );

        return response()->json($result);
    }

    /**
     * Enable offline mode
     */
    public function enableOfflineMode(Request $request): JsonResponse
    {
        $result = $this->mobileService->enableOfflineMode(
            auth()->id(),
            $request->all()
        );

        return response()->json($result);
    }

    /**
     * Sync offline data
     */
    public function syncOfflineData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'offline_data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->mobileService->syncOfflineData(
            auth()->id(),
            $request->offline_data
        );

        return response()->json($result);
    }

    /**
     * Get mobile app configuration
     */
    public function getMobileConfig(Request $request): JsonResponse
    {
        $platform = $request->get('platform', 'flutter');
        $config = $this->mobileService->getMobileAppConfig($platform);

        return response()->json([
            'success' => true,
            'data' => $config,
            'message' => 'Mobile configuration retrieved successfully'
        ]);
    }

    /**
     * Check mobile app version compatibility
     */
    public function checkVersionCompatibility(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_version' => 'required|string',
            'platform' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->mobileService->checkVersionCompatibility(
            $request->current_version,
            $request->platform
        );

        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => 'Version compatibility checked successfully'
        ]);
    }
}