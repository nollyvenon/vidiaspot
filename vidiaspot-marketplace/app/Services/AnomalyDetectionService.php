<?php

namespace App\Services;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnomalyDetectionService
{
    /**
     * Thresholds for anomaly detection
     */
    private array $thresholds = [
        'login_attempts_per_hour' => 10,
        'failed_login_attempts_per_hour' => 5,
        'location_changes_per_hour' => 5,
        'transaction_frequency_per_hour' => 20,
        'suspicious_ip_attempts_per_hour' => 3,
        'unusual_device_login' => 1, // New device detection
    ];
    
    /**
     * Check for anomalies in user behavior
     */
    public function checkUserAnomalies(User $user): array
    {
        $anomalies = [];
        
        // Check for unusual login patterns
        $anomalies = array_merge($anomalies, $this->checkLoginAnomalies($user));
        
        // Check for location anomalies
        $anomalies = array_merge($anomalies, $this->checkLocationAnomalies($user));
        
        // Check for transaction anomalies
        $anomalies = array_merge($anomalies, $this->checkTransactionAnomalies($user));
        
        // Check for IP anomalies
        $anomalies = array_merge($anomalies, $this->checkIPAnomalies($user));
        
        // Check for device anomalies
        $anomalies = array_merge($anomalies, $this->checkDeviceAnomalies($user));
        
        return $anomalies;
    }
    
    /**
     * Check login-related anomalies
     */
    private function checkLoginAnomalies(User $user): array
    {
        $anomalies = [];
        
        $timeFrame = Carbon::now()->subHour();
        
        // Count successful logins in the last hour
        $loginCount = Activity::where('user_id', $user->id)
            ->where('activity', 'login')
            ->where('created_at', '>=', $timeFrame)
            ->count();
        
        if ($loginCount > $this->thresholds['login_attempts_per_hour']) {
            $anomalies[] = [
                'type' => 'excessive_logins',
                'severity' => 'medium',
                'message' => "User has {$loginCount} logins in the past hour",
                'timestamp' => now(),
                'data' => ['login_count' => $loginCount]
            ];
        }
        
        // Count failed login attempts
        $failedLoginCount = Activity::where('user_id', $user->id)
            ->where('activity', 'login_failed')
            ->where('created_at', '>=', $timeFrame)
            ->count();
        
        if ($failedLoginCount > $this->thresholds['failed_login_attempts_per_hour']) {
            $anomalies[] = [
                'type' => 'excessive_failed_logins',
                'severity' => 'high',
                'message' => "User has {$failedLoginCount} failed login attempts in the past hour",
                'timestamp' => now(),
                'data' => ['failed_login_count' => $failedLoginCount]
            ];
        }
        
        return $anomalies;
    }
    
    /**
     * Check location-based anomalies
     */
    private function checkLocationAnomalies(User $user): array
    {
        $anomalies = [];
        
        $timeFrame = Carbon::now()->subHour();
        
        // Count distinct locations in the last hour
        $locationCount = Activity::where('user_id', $user->id)
            ->where('created_at', '>=', $timeFrame)
            ->whereNotNull('location')
            ->distinct('location')
            ->count('location');
        
        if ($locationCount > $this->thresholds['location_changes_per_hour']) {
            $anomalies[] = [
                'type' => 'location_anomaly',
                'severity' => 'high',
                'message' => "User accessed from {$locationCount} different locations in the past hour",
                'timestamp' => now(),
                'data' => ['location_count' => $locationCount]
            ];
        }
        
        return $anomalies;
    }
    
    /**
     * Check transaction-related anomalies
     */
    private function checkTransactionAnomalies(User $user): array
    {
        $anomalies = [];
        
        $timeFrame = Carbon::now()->subHour();
        
        // Count transactions in the last hour
        $transactionCount = \DB::table('payment_transactions')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $timeFrame)
            ->count();
        
        if ($transactionCount > $this->thresholds['transaction_frequency_per_hour']) {
            $anomalies[] = [
                'type' => 'transaction_anomaly',
                'severity' => 'medium',
                'message' => "User has {$transactionCount} transactions in the past hour",
                'timestamp' => now(),
                'data' => ['transaction_count' => $transactionCount]
            ];
        }
        
        return $anomalies;
    }
    
    /**
     * Check IP address anomalies
     */
    private function checkIPAnomalies(User $user): array
    {
        $anomalies = [];
        
        $timeFrame = Carbon::now()->subHour();
        
        // Count distinct IP addresses in the last hour
        $ipCount = Activity::where('user_id', $user->id)
            ->where('created_at', '>=', $timeFrame)
            ->whereNotNull('ip_address')
            ->distinct('ip_address')
            ->count('ip_address');
        
        if ($ipCount > $this->thresholds['suspicious_ip_attempts_per_hour']) {
            $anomalies[] = [
                'type' => 'ip_anomaly',
                'severity' => 'high',
                'message' => "User accessed from {$ipCount} different IP addresses in the past hour",
                'timestamp' => now(),
                'data' => ['ip_count' => $ipCount]
            ];
        }
        
        return $anomalies;
    }
    
    /**
     * Check for new device logins
     */
    private function checkDeviceAnomalies(User $user): array
    {
        $anomalies = [];
        
        // Check for new device login (not seen in last 30 days)
        $recentDevices = Activity::where('user_id', $user->id)
            ->where('activity', 'login')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('user_agent')
            ->distinct('user_agent')
            ->pluck('user_agent')
            ->toArray();
        
        // Find activities with user agents not in recent devices
        $newDeviceActivities = Activity::where('user_id', $user->id)
            ->where('activity', 'login')
            ->where('created_at', '>=', Carbon::now()->subHours(1))
            ->whereNotIn('user_agent', $recentDevices)
            ->get();
        
        if ($newDeviceActivities->count() > 0) {
            $anomalies[] = [
                'type' => 'new_device_login',
                'severity' => 'medium',
                'message' => "User logged in from new device",
                'timestamp' => now(),
                'data' => ['device_count' => $newDeviceActivities->count()]
            ];
        }
        
        return $anomalies;
    }
    
    /**
     * Process and record anomalies if detected
     */
    public function processAnomalies(User $user): bool
    {
        $anomalies = $this->checkUserAnomalies($user);
        
        if (!empty($anomalies)) {
            foreach ($anomalies as $anomaly) {
                // Log the anomaly
                Log::warning("Anomaly detected for user {$user->id}", $anomaly);
                
                // You could store the anomaly in a database or trigger an alert
                $this->recordAnomaly($user, $anomaly);
                
                // For high severity anomalies, you might want to take additional actions
                if ($anomaly['severity'] === 'high') {
                    $this->handleHighSeverityAnomaly($user, $anomaly);
                }
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Record the detected anomaly in the database
     */
    private function recordAnomaly(User $user, array $anomaly): void
    {
        // Create an anomaly record (you'd need to create an anomalies table)
        \DB::table('anomalies')->insert([
            'user_id' => $user->id,
            'type' => $anomaly['type'],
            'severity' => $anomaly['severity'],
            'message' => $anomaly['message'],
            'data' => json_encode($anomaly['data']),
            'created_at' => $anomaly['timestamp'],
            'updated_at' => $anomaly['timestamp'],
        ]);
    }
    
    /**
     * Handle high severity anomalies
     */
    private function handleHighSeverityAnomaly(User $user, array $anomaly): void
    {
        // You might want to temporarily lock the account or require additional verification
        Log::critical("High severity anomaly detected for user {$user->id}", $anomaly);
        
        // Possible actions:
        // - Send notification to user
        // - Temporarily lock account
        // - Require additional verification
        // - Notify admin
    }
    
    /**
     * Set custom threshold for anomaly detection
     */
    public function setThreshold(string $type, int $value): void
    {
        if (array_key_exists($type, $this->thresholds)) {
            $this->thresholds[$type] = $value;
        }
    }
    
    /**
     * Get all thresholds
     */
    public function getThresholds(): array
    {
        return $this->thresholds;
    }
}