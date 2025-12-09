<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class DeviceFingerprintingService
{
    /**
     * Generate a device fingerprint from request data
     */
    public function generateFingerprint(Request $request): string
    {
        $userAgent = $request->userAgent() ?? '';
        $acceptLanguage = $request->header('Accept-Language') ?? '';
        $acceptEncoding = $request->header('Accept-Encoding') ?? '';
        $acceptCharset = $request->header('Accept-Charset') ?? '';
        $screenResolution = $request->header('X-Screen-Resolution') ?? '';
        $timezone = $request->header('X-Timezone') ?? '';
        $platform = $request->header('X-Platform') ?? '';
        
        // Use additional client hints if available (from JavaScript)
        $clientHints = [
            'sec-ch-ua' => $request->header('Sec-CH-UA') ?? '',
            'sec-ch-ua-platform' => $request->header('Sec-CH-UA-Platform') ?? '',
            'sec-ch-ua-mobile' => $request->header('Sec-CH-UA-Mobile') ?? '',
            'device-memory' => $request->header('Device-Memory') ?? '',
            'dpr' => $request->header('DPR') ?? '',
            'viewport-width' => $request->header('Viewport-Width') ?? '',
        ];
        
        // Create a unique string from all the device characteristics
        $fingerprintString = implode('|', [
            $userAgent,
            $acceptLanguage,
            $acceptEncoding,
            $acceptCharset,
            $screenResolution,
            $timezone,
            $platform,
            serialize($clientHints)
        ]);
        
        // Generate a hash of the fingerprint string
        return Hash::make($fingerprintString);
    }

    /**
     * Create a more advanced device fingerprint using agent detection
     */
    public function generateAdvancedFingerprint(Request $request): array
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        
        // Extract device information
        $deviceInfo = [
            'user_agent' => $request->userAgent(),
            'platform' => $agent->platform(),
            'platform_version' => $agent->version($agent->platform()),
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'device_type' => $this->getDeviceType($agent),
            'is_desktop' => $agent->isDesktop(),
            'is_phone' => $agent->isPhone(),
            'is_tablet' => $agent->isTablet(),
            'accept_language' => $request->header('Accept-Language'),
            'accept_encoding' => $request->header('Accept-Encoding'),
            'accept_charset' => $request->header('Accept-Charset'),
            'ip_address' => $request->ip(),
            'ip_forwarded' => $request->header('X-Forwarded-For') ?? null,
            'remote_addr' => $request->server('REMOTE_ADDR') ?? null,
            'http_via' => $request->header('Via') ?? null,
            'http_x_forwarded_for' => $request->header('X-Forwarded-For') ?? null,
            'http_x_real_ip' => $request->header('X-Real-IP') ?? null,
            'client_hints' => [
                'sec_ch_ua' => $request->header('Sec-CH-UA'),
                'sec_ch_ua_platform' => $request->header('Sec-CH-UA-Platform'),
                'sec_ch_ua_mobile' => $request->header('Sec-CH-UA-Mobile'),
                'device_memory' => $request->header('Device-Memory'),
                'dpr' => $request->header('DPR'),
                'viewport_width' => $request->header('Viewport-Width'),
            ],
        ];
        
        // Generate a unique device ID based on the device characteristics
        $deviceId = $this->generateDeviceId($deviceInfo);
        
        return [
            'device_id' => $deviceId,
            'fingerprint' => $this->createFingerprintHash($deviceInfo),
            'device_info' => $deviceInfo,
            'timestamp' => now(),
        ];
    }

    /**
     * Get device type based on agent
     */
    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isPhone()) {
            return 'phone';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Generate a device ID
     */
    private function generateDeviceId(array $deviceInfo): string
    {
        $identifier = [
            $deviceInfo['platform'],
            $deviceInfo['platform_version'],
            $deviceInfo['browser'],
            $deviceInfo['browser_version'],
            $deviceInfo['device_type'],
            $deviceInfo['accept_language'],
        ];
        
        return 'device_' . Str::slug(implode('_', $identifier)) . '_' . Str::random(8);
    }

    /**
     * Create a fingerprint hash from device info
     */
    private function createFingerprintHash(array $deviceInfo): string
    {
        $hashable = [
            $deviceInfo['user_agent'],
            $deviceInfo['platform'],
            $deviceInfo['browser'],
            $deviceInfo['accept_language'],
            $deviceInfo['client_hints']['sec_ch_ua'] ?? '',
            $deviceInfo['client_hints']['sec_ch_ua_platform'] ?? '',
            $deviceInfo['client_hints']['device_memory'] ?? '',
            $deviceInfo['client_hints']['dpr'] ?? '',
        ];
        
        return hash('sha256', implode('|', $hashable));
    }

    /**
     * Store device fingerprint for a user
     */
    public function storeDeviceForUser(User $user, string $fingerprint, array $deviceInfo = []): void
    {
        $cacheKey = "user_devices_{$user->id}";
        $devices = Cache::get($cacheKey, []);
        
        // Check if this device already exists
        if (!in_array($fingerprint, $devices)) {
            $devices[] = [
                'fingerprint' => $fingerprint,
                'info' => $deviceInfo,
                'last_seen' => now()->toISOString(),
                'first_seen' => now()->toISOString(),
            ];
            
            // Keep only the last 20 devices to prevent cache bloat
            if (count($devices) > 20) {
                $devices = array_slice($devices, -20);
            }
            
            Cache::put($cacheKey, $devices, now()->addDays(30));
        } else {
            // Update the last seen time for existing device
            foreach ($devices as &$device) {
                if ($device['fingerprint'] === $fingerprint) {
                    $device['last_seen'] = now()->toISOString();
                    break;
                }
            }
            
            Cache::put($cacheKey, $devices, now()->addDays(30));
        }
    }

    /**
     * Check if a device fingerprint has been seen for a user
     */
    public function isKnownDeviceForUser(User $user, string $fingerprint): bool
    {
        $cacheKey = "user_devices_{$user->id}";
        $devices = Cache::get($cacheKey, []);
        
        foreach ($devices as $device) {
            if ($device['fingerprint'] === $fingerprint) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get all devices for a user
     */
    public function getUserDevices(User $user): array
    {
        $cacheKey = "user_devices_{$user->id}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Detect if the current request is from a suspicious device
     */
    public function isSuspiciousDevice(Request $request, User $user = null): array
    {
        $result = [
            'is_suspicious' => false,
            'reasons' => [],
        ];
        
        if (!$user) {
            return $result;
        }
        
        $advancedFingerprint = $this->generateAdvancedFingerprint($request);
        $isKnown = $this->isKnownDeviceForUser($user, $advancedFingerprint['fingerprint']);
        
        if (!$isKnown) {
            $result['is_suspicious'] = true;
            $result['reasons'][] = 'new_device';
        }
        
        // Check for other suspicious patterns
        $userAgent = $request->userAgent();
        if ($userAgent && $this->isBotUserAgent($userAgent)) {
            $result['is_suspicious'] = true;
            $result['reasons'][] = 'bot_user_agent';
        }
        
        // Check for common automation tools
        $headersToCheck = [
            'X-Powered-By',
            'X-Requested-With',
            'X-Forwarded-Proto',
            'X-Forwarded-Host',
        ];
        
        foreach ($headersToCheck as $header) {
            $headerValue = $request->header($header);
            if ($headerValue && $this->isAutomationHeader($headerValue)) {
                $result['is_suspicious'] = true;
                $result['reasons'][] = 'automation_header';
                break;
            }
        }
        
        return $result;
    }

    /**
     * Check if user agent is a bot
     */
    private function isBotUserAgent(string $userAgent): bool
    {
        $botPatterns = [
            '/bot/i',
            '/crawl/i',
            '/spider/i',
            '/facebookexternalhit/i',
            '/twitterbot/i',
            '/linkedinbot/i',
            '/pinterest/i',
            '/whatsapp/i',
        ];
        
        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if header indicates automation
     */
    private function isAutomationHeader(string $headerValue): bool
    {
        $automationIndicators = [
            'axios',
            'node-fetch',
            'python-requests',
            'curl',
            'wget',
            'postman',
        ];
        
        foreach ($automationIndicators as $indicator) {
            if (stripos($headerValue, $indicator) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Track device login/usage for anomaly detection
     */
    public function trackDeviceActivity(Request $request, User $user, string $activityType): void
    {
        $fingerprint = $this->generateAdvancedFingerprint($request);
        
        // Store device info for the user
        $this->storeDeviceForUser($user, $fingerprint['fingerprint'], $fingerprint['device_info']);
        
        // Log the activity with device info for potential anomaly detection
        // In a real implementation, you would store this in a database
        Log::info('Device activity tracked', [
            'user_id' => $user->id,
            'activity' => $activityType,
            'device_fingerprint' => $fingerprint['fingerprint'],
            'device_info' => [
                'browser' => $fingerprint['device_info']['browser'],
                'platform' => $fingerprint['device_info']['platform'],
                'is_desktop' => $fingerprint['device_info']['is_desktop'],
                'is_phone' => $fingerprint['device_info']['is_phone'],
                'is_tablet' => $fingerprint['device_info']['is_tablet'],
            ],
            'ip_address' => $request->ip(),
            'timestamp' => now(),
        ]);
    }
}