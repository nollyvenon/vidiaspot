<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BiometricAuthorizationService
{
    /**
     * Register a biometric template for a user
     */
    public function registerBiometricTemplate(User $user, string $biometricData, string $templateType = 'fingerprint'): array
    {
        // In a real implementation, this would interface with a biometric SDK/API
        // For this example, we'll simulate the process
        
        // Generate a unique template ID
        $templateId = 'bio_' . Str::uuid();
        
        // Hash the biometric data for storage (in real implementation, you'd use proper encryption)
        $hashedBiometricData = Hash::make($biometricData);
        
        // Store the biometric template in the database
        $biometricRecord = [
            'id' => $templateId,
            'user_id' => $user->id,
            'template_type' => $templateType, // fingerprint, face, iris, voice
            'template_data' => $hashedBiometricData,
            'device_info' => $this->getCurrentDeviceInfo(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        // In a real implementation, you would store this in a proper database table
        // For now, we'll use cache as a placeholder
        $cacheKey = "user_biometric_templates_{$user->id}";
        $templates = Cache::get($cacheKey, []);
        $templates[$templateId] = $biometricRecord;
        Cache::put($cacheKey, $templates, now()->addYears(5));
        
        return [
            'template_id' => $templateId,
            'success' => true,
            'message' => 'Biometric template registered successfully',
            'template_type' => $templateType
        ];
    }

    /**
     * Verify biometric data against stored templates
     */
    public function verifyBiometricData(User $user, string $biometricData, string $templateType = 'fingerprint'): array
    {
        // Get all active biometric templates for the user
        $templates = $this->getUserBiometricTemplates($user);
        
        if (empty($templates)) {
            return [
                'verified' => false,
                'error' => 'No biometric templates registered for this user',
            ];
        }
        
        // Filter by template type if specified
        $matchingTemplates = array_filter($templates, function ($template) use ($templateType) {
            return $template['template_type'] === $templateType && $template['is_active'];
        });
        
        if (empty($matchingTemplates)) {
            return [
                'verified' => false,
                'error' => 'No active biometric templates of specified type found',
            ];
        }
        
        // In a real implementation, this would involve comparing biometric data
        // with stored templates using specialized algorithms
        // For this example, we'll simulate the verification
        
        foreach ($matchingTemplates as $template) {
            // In real implementation, perform biometric matching
            $matchResult = $this->compareBiometricData($biometricData, $template['template_data']);
            
            if ($matchResult['match']) {
                // Log successful verification
                $this->logBiometricVerification($user->id, $template['id'], true);
                
                return [
                    'verified' => true,
                    'template_id' => $template['id'],
                    'confidence' => $matchResult['confidence'],
                    'message' => 'Biometric verification successful'
                ];
            }
        }
        
        // Log failed verification
        $this->logBiometricVerification($user->id, null, false);
        
        return [
            'verified' => false,
            'error' => 'Biometric data does not match any registered template',
        ];
    }

    /**
     * Compare biometric data with stored template
     * This is a placeholder implementation - in reality this would be handled by a specialized biometric SDK
     */
    private function compareBiometricData(string $inputData, string $storedTemplate): array
    {
        // In a real implementation, this would use a biometric comparison algorithm
        // For this example, we'll return a random match result based on similarity
        
        // Simulate biometric matching with a confidence score
        $similarity = $this->calculateBiometricSimilarity($inputData, $storedTemplate);
        
        return [
            'match' => $similarity > 0.8, // 80% threshold for match
            'confidence' => $similarity,
        ];
    }

    /**
     * Calculate biometric similarity (placeholder implementation)
     */
    private function calculateBiometricSimilarity(string $data1, string $data2): float
    {
        // This is a simplified placeholder - real biometric matching is much more complex
        // In a real implementation, you would use proper biometric feature extraction and matching algorithms
        $hash1 = md5($data1);
        $hash2 = md5($data2);
        
        // Calculate similarity based on character matches
        $matches = 0;
        $length = min(strlen($hash1), strlen($hash2));
        
        for ($i = 0; $i < $length; $i++) {
            if ($hash1[$i] === $hash2[$i]) {
                $matches++;
            }
        }
        
        return $matches / $length;
    }

    /**
     * Get all biometric templates for a user
     */
    public function getUserBiometricTemplates(User $user): array
    {
        $cacheKey = "user_biometric_templates_{$user->id}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Authorize a transaction using biometric verification
     */
    public function authorizeTransaction(User $user, string $biometricData, array $transactionData): array
    {
        // First verify the biometric data
        $verificationResult = $this->verifyBiometricData($user, $biometricData);
        
        if (!$verificationResult['verified']) {
            return [
                'authorized' => false,
                'error' => $verificationResult['error'],
                'verification' => $verificationResult,
            ];
        }
        
        // Additional security checks
        $securityCheck = $this->performSecurityChecks($user, $transactionData);
        
        if (!$securityCheck['allowed']) {
            return [
                'authorized' => false,
                'error' => $securityCheck['reason'],
                'verification' => $verificationResult,
            ];
        }
        
        // Log the authorized transaction
        $this->logBiometricTransaction($user->id, $verificationResult['template_id'], $transactionData);
        
        return [
            'authorized' => true,
            'verification' => $verificationResult,
            'transaction_id' => $transactionData['id'] ?? Str::uuid(),
            'message' => 'Transaction authorized successfully with biometric verification'
        ];
    }

    /**
     * Perform additional security checks before authorizing transaction
     */
    private function performSecurityChecks(User $user, array $transactionData): array
    {
        // Check transaction amount against user's limits
        $amount = $transactionData['amount'] ?? 0;
        $userMaxAmount = $user->subscription?->biometric_transaction_limit ?? 1000000; // Default to 1M naira
        
        if ($amount > $userMaxAmount) {
            return [
                'allowed' => false,
                'reason' => 'Transaction amount exceeds biometric authorization limit'
            ];
        }
        
        // Check for suspicious activity patterns
        $recentTransactions = $this->getRecentBiometricTransactions($user->id, 30); // Last 30 minutes
        
        if (count($recentTransactions) > 5) {
            return [
                'allowed' => false,
                'reason' => 'Too many biometric transactions in short time period'
            ];
        }
        
        return ['allowed' => true];
    }

    /**
     * Get recent biometric transactions for a user
     */
    private function getRecentBiometricTransactions(int $userId, int $minutes = 30): array
    {
        $cacheKey = "biometric_transactions_{$userId}_recent";
        $transactions = Cache::get($cacheKey, []);
        
        // Filter transactions from the last $minutes minutes
        $recent = array_filter($transactions, function ($transaction) use ($minutes) {
            return strtotime($transaction['timestamp']) >= strtotime("-{$minutes} minutes");
        });
        
        return $recent;
    }

    /**
     * Log biometric verification event
     */
    private function logBiometricVerification(int $userId, ?string $templateId, bool $success): void
    {
        $logData = [
            'user_id' => $userId,
            'template_id' => $templateId,
            'success' => $success,
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip() ?? 'unknown',
            'user_agent' => request()->userAgent() ?? 'unknown',
        ];
        
        Log::info('Biometric verification', $logData);
        
        // Store in cache for recent activity tracking
        $logKey = "biometric_verifications_{$userId}";
        $logs = Cache::get($logKey, []);
        $logs[] = $logData;
        
        // Keep only last 100 verification logs
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }
        
        Cache::put($logKey, $logs, now()->addDays(7));
    }

    /**
     * Log biometric transaction event
     */
    private function logBiometricTransaction(int $userId, string $templateId, array $transactionData): void
    {
        $logData = [
            'user_id' => $userId,
            'template_id' => $templateId,
            'transaction_id' => $transactionData['id'] ?? Str::uuid(),
            'amount' => $transactionData['amount'] ?? 0,
            'type' => $transactionData['type'] ?? 'payment',
            'timestamp' => now()->toISOString(),
        ];
        
        Log::info('Biometric transaction authorization', $logData);
        
        // Add to recent transactions cache
        $cacheKey = "biometric_transactions_{$userId}_recent";
        $transactions = Cache::get($cacheKey, []);
        $transactions[] = $logData;
        
        // Keep only last 50 transactions
        if (count($transactions) > 50) {
            $transactions = array_slice($transactions, -50);
        }
        
        Cache::put($cacheKey, $transactions, now()->addHours(1));
    }

    /**
     * Get current device information for registration
     */
    private function getCurrentDeviceInfo(): array
    {
        return [
            'user_agent' => request()?->userAgent() ?? 'unknown',
            'ip_address' => request()?->ip() ?? 'unknown',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Update biometric template status
     */
    public function updateTemplateStatus(User $user, string $templateId, bool $isActive): bool
    {
        $cacheKey = "user_biometric_templates_{$user->id}";
        $templates = Cache::get($cacheKey, []);
        
        if (!isset($templates[$templateId])) {
            return false;
        }
        
        $templates[$templateId]['is_active'] = $isActive;
        $templates[$templateId]['updated_at'] = now();
        
        Cache::put($cacheKey, $templates, now()->addYears(5));
        
        return true;
    }

    /**
     * Delete a biometric template
     */
    public function deleteTemplate(User $user, string $templateId): bool
    {
        $cacheKey = "user_biometric_templates_{$user->id}";
        $templates = Cache::get($cacheKey, []);
        
        if (!isset($templates[$templateId])) {
            return false;
        }
        
        unset($templates[$templateId]);
        Cache::put($cacheKey, $templates, now()->addYears(5));
        
        return true;
    }
}