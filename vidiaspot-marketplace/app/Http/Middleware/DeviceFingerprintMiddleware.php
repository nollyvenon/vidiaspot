<?php

namespace App\Http\Middleware;

use App\Services\DeviceFingerprintingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DeviceFingerprintMiddleware
{
    protected DeviceFingerprintingService $deviceFingerprintingService;

    public function __construct(DeviceFingerprintingService $deviceFingerprintingService)
    {
        $this->deviceFingerprintingService = $deviceFingerprintingService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for certain routes that don't require device tracking
        if ($this->shouldSkipFingerprinting($request)) {
            return $next($request);
        }

        // Get authenticated user if available
        $user = Auth::user();

        // Generate device fingerprint
        $fingerprint = $this->deviceFingerprintingService->generateAdvancedFingerprint($request);

        // Store in request for later use
        $request->attributes->set('device_fingerprint', $fingerprint);

        // If user is authenticated, track device activity
        if ($user) {
            $activityType = $this->getActivityType($request);
            $this->deviceFingerprintingService->trackDeviceActivity($request, $user, $activityType);

            // Check if device is suspicious
            $suspiciousCheck = $this->deviceFingerprintingService->isSuspiciousDevice($request, $user);
            if ($suspiciousCheck['is_suspicious']) {
                // Log suspicious activity
                \Log::warning('Suspicious device detected', [
                    'user_id' => $user->id,
                    'fingerprint' => $fingerprint['fingerprint'],
                    'reasons' => $suspiciousCheck['reasons'],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl()
                ]);
            }
        }

        return $next($request);
    }

    /**
     * Determine if device fingerprinting should be skipped for this request
     */
    private function shouldSkipFingerprinting(Request $request): bool
    {
        $skipPaths = [
            'api/user', // Avoid infinite loop
            'api/2fa/*', // 2FA routes
            'api/blockchain-verification/*', // Blockchain verification
            'api/payment-tokenization/*', // Payment tokenization
            'sanctum/*', // Sanctum authentication
            'login', // Login route
            'register', // Registration route
            'forgot-password', // Password reset
        ];

        foreach ($skipPaths as $path) {
            if ($request->is($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the type of activity based on the request
     */
    private function getActivityType(Request $request): string
    {
        if ($request->isMethod('POST')) {
            if ($request->is('*/login') || $request->is('auth/*')) {
                return 'login';
            }
            if ($request->is('*/payment*')) {
                return 'payment';
            }
            if ($request->is('*/transaction*')) {
                return 'transaction';
            }
        } elseif ($request->isMethod('GET')) {
            if ($request->is('*/profile') || $request->is('*/settings')) {
                return 'profile_access';
            }
        }

        return 'general_access';
    }
}