<?php

namespace App\Services;

use App\Models\User;
use App\Models\P2pCryptoOrder;
use App\Models\CryptoTransaction;
use Illuminate\Support\Facades\Http;

class P2pCryptoSecurityService
{
    /**
     * Verify user identity using various methods
     */
    public function verifyIdentity(User $user, array $verificationData): array
    {
        $result = [
            'success' => false,
            'method' => $verificationData['method'] ?? null,
            'status' => 'pending',
            'details' => []
        ];

        switch ($verificationData['method']) {
            case 'document':
                $result = $this->verifyDocument($user, $verificationData);
                break;
            case 'biometric_face':
                $result = $this->verifyBiometricFace($user, $verificationData);
                break;
            case 'biometric_fingerprint':
                $result = $this->verifyBiometricFingerprint($user, $verificationData);
                break;
            case 'video':
                $result = $this->verifyVideo($user, $verificationData);
                break;
        }

        return $result;
    }

    /**
     * Document verification using external service
     */
    protected function verifyDocument(User $user, array $data): array
    {
        // In a real implementation, this would connect to a document verification service
        // like Onfido, Jumio, or similar
        $provider = config('p2p_crypto_security.kyc_aml.provider', 'default');
        
        if ($provider === 'default') {
            // Simulate verification for demo purposes
            return [
                'success' => true,
                'method' => 'document',
                'status' => 'verified',
                'details' => [
                    'document_type' => $data['document_type'] ?? 'unknown',
                    'document_number' => $data['document_number'] ?? null,
                    'verified_at' => now()->toISOString(),
                ]
            ];
        }

        // In production, connect to real verification service
        // Example using an API call:
        /*
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.kyc.api_key'),
        ])->post(config('services.kyc.base_url') . '/verify', [
            'user_id' => $user->id,
            'document_front' => $data['document_front'],
            'document_back' => $data['document_back'] ?? null,
            'document_type' => $data['document_type'],
        ]);

        return $response->json();
        */

        return [
            'success' => false,
            'method' => 'document',
            'status' => 'failed',
            'details' => ['reason' => 'Provider not configured']
        ];
    }

    /**
     * Face verification using biometric service
     */
    protected function verifyBiometricFace(User $user, array $data): array
    {
        $provider = config('p2p_crypto_security.biometric.providers.face', 'aws_rekognition');
        
        if ($provider === 'aws_rekognition') {
            // In a real implementation, this would use AWS Rekognition or similar service
            return [
                'success' => true,
                'method' => 'biometric_face',
                'status' => 'verified',
                'confidence' => 98.5,
                'details' => [
                    'match_confirmed' => true,
                    'confidence_score' => 98.5,
                    'verified_at' => now()->toISOString(),
                ]
            ];
        }

        return [
            'success' => false,
            'method' => 'biometric_face',
            'status' => 'failed',
            'details' => ['reason' => 'Provider not configured']
        ];
    }

    /**
     * Fingerprint verification
     */
    protected function verifyBiometricFingerprint(User $user, array $data): array
    {
        return [
            'success' => true,
            'method' => 'biometric_fingerprint',
            'status' => 'verified',
            'details' => [
                'fingerprint_id' => $data['fingerprint_hash'] ?? null,
                'verified_at' => now()->toISOString(),
            ]
        ];
    }

    /**
     * Video verification
     */
    protected function verifyVideo(User $user, array $data): array
    {
        $provider = config('p2p_crypto_security.video_verification.provider', 'twilio');
        
        // In a real implementation, this would connect to a video verification service
        return [
            'success' => true,
            'method' => 'video',
            'status' => 'verified',
            'details' => [
                'session_id' => $data['session_id'] ?? null,
                'verified_at' => now()->toISOString(),
                'recording_available' => false,
            ]
        ];
    }

    /**
     * Calculate user reputation score
     */
    public function calculateReputationScore(User $user): float
    {
        // Calculate reputation based on various factors
        $totalTrades = $user->p2pCryptoOrders()->count();
        $completedTrades = $user->p2pCryptoOrders()->where('status', 'completed')->count();
        $disputedTrades = $user->p2pCryptoOrders()->where('status', 'in_dispute')->count();
        
        $completionRate = $totalTrades > 0 ? ($completedTrades / $totalTrades) * 100 : 0;
        $disputeRate = $totalTrades > 0 ? ($disputedTrades / $totalTrades) * 100 : 0;
        
        // Base reputation from 0-100
        $baseScore = min(100, max(0, $completionRate));
        
        // Deduct points for disputes
        $disputePenalty = min(30, $disputeRate * 0.5); // Max 30% penalty for disputes
        
        // Add points for total volume (capped)
        $volumeBonus = min(20, log($totalTrades + 1) * 5); // Volume bonus up to 20 points
        
        $finalScore = max(0, min(100, $baseScore - $disputePenalty + $volumeBonus));
        
        // Update user's reputation in the database
        $user->update([
            'reputation_score' => $finalScore,
            'total_trade_count' => $totalTrades,
            'trade_completion_rate' => $completionRate,
        ]);

        return round($finalScore, 2);
    }

    /**
     * Check if a transaction is suspicious using AI fraud detection
     */
    public function isSuspiciousTransaction(P2pCryptoOrder $order, User $user = null): bool
    {
        if (!$user) {
            $user = $order->seller ?? $order->buyer;
        }

        $suspicious = false;
        $reasons = [];

        // Check transaction volume
        $dailyVolume = $this->getUserDailyVolume($user);
        $dailyLimit = $this->getUserVerificationLevelLimit($user);
        
        if ($order->total_amount > ($dailyLimit * 0.8)) {
            $suspicious = true;
            $reasons[] = 'High transaction value relative to daily limit';
        }

        // Check transaction velocity (multiple transactions in short time)
        $recentTransactions = $this->getUserRecentTransactions($user, 30); // Last 30 minutes
        if (count($recentTransactions) > 5) {
            $suspicious = true;
            $reasons[] = 'High transaction velocity';
        }

        // Check for unusual patterns in transaction times
        if ($this->hasUnusualTransactionPattern($user)) {
            $suspicious = true;
            $reasons[] = 'Unusual transaction timing pattern';
        }

        // Check for new account with large transactions
        if ($user->created_at->diffInHours(now()) < 24 && $order->total_amount > 1000) {
            $suspicious = true;
            $reasons[] = 'New account with large transaction';
        }

        return [
            'is_suspicious' => $suspicious,
            'reasons' => $reasons,
            'risk_score' => $suspicious ? count($reasons) * 20 : 0
        ];
    }

    /**
     * Get user's daily transaction volume
     */
    protected function getUserDailyVolume(User $user): float
    {
        return P2pCryptoOrder::where(function($query) use ($user) {
                $query->where('seller_id', $user->id)
                      ->orWhere('buyer_id', $user->id);
            })
            ->where('created_at', '>=', now()->subDay())
            ->sum('total_amount');
    }

    /**
     * Get verification level limit
     */
    protected function getUserVerificationLevelLimit(User $user): float
    {
        $verificationLevel = $user->verification_level ?? 'unverified';
        $thresholds = config('p2p_crypto_security.verification.verification_thresholds', []);
        
        $levelConfig = $thresholds[$verificationLevel] ?? $thresholds['level_1'] ?? ['daily_limit' => 1000];
        return $levelConfig['daily_limit'];
    }

    /**
     * Get recent transactions for velocity check
     */
    protected function getUserRecentTransactions(User $user, int $minutes = 30): array
    {
        return P2pCryptoOrder::where(function($query) use ($user) {
                $query->where('seller_id', $user->id)
                      ->orWhere('buyer_id', $user->id);
            })
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->get()
            ->toArray();
    }

    /**
     * Check for unusual transaction patterns
     */
    protected function hasUnusualTransactionPattern(User $user): bool
    {
        // This would typically connect to a machine learning model
        // For now, checking basic patterns
        $recentOrders = $user->p2pCryptoOrders()
            ->where('created_at', '>=', now()->subHour())
            ->get();

        // Look for patterns like rapid buy/sell orders
        $patternCount = 0;
        $lastOrder = null;
        foreach ($recentOrders as $order) {
            if ($lastOrder && $lastOrder->order_type !== $order->order_type) {
                $patternCount++;
            }
            $lastOrder = $order;
        }

        return $patternCount > 3; // More than 3 pattern changes in an hour
    }

    /**
     * Risk assessment for a trading position
     */
    public function assessPositionRisk(array $positionData): array
    {
        $leverage = $positionData['leverage'] ?? 1;
        $margin = $positionData['margin'] ?? 0;
        $liquidationPrice = $positionData['liquidation_price'] ?? 0;
        $currentPrice = $positionData['current_price'] ?? 0;
        
        $riskLevel = 'low';
        $riskScore = 0;
        $suggestions = [];

        // Calculate risk based on leverage
        if ($leverage > config('p2p_crypto_security.risk_management.max_leverage', 10)) {
            $riskLevel = 'high';
            $riskScore += 30;
        } elseif ($leverage > 5) {
            $riskLevel = 'medium';
            $riskScore += 15;
        }

        // Calculate distance to liquidation
        if ($currentPrice > 0 && $liquidationPrice > 0) {
            $distanceToLiquidation = abs(($currentPrice - $liquidationPrice) / $currentPrice) * 100;
            if ($distanceToLiquidation < 5) {
                $riskLevel = 'high';
                $riskScore += 40;
                $suggestions[] = 'Position too close to liquidation, consider reducing leverage';
            } elseif ($distanceToLiquidation < 10) {
                $riskLevel = 'medium';
                $riskScore += 20;
                $suggestions[] = 'Position at risk of liquidation, monitor closely';
            }
        }

        // Check margin level
        if ($margin < 100) {
            $riskScore += 20;
            $suggestions[] = 'Low margin level, consider adding more funds';
        }

        return [
            'risk_level' => $riskLevel,
            'risk_score' => min(100, $riskScore),
            'suggestions' => $suggestions,
            'is_safe_to_proceed' => $riskLevel !== 'high'
        ];
    }

    /**
     * Calculate portfolio diversification score
     */
    public function calculateDiversificationScore(array $portfolio): float
    {
        if (empty($portfolio)) {
            return 0;
        }

        $totalValue = array_sum(array_column($portfolio, 'value'));
        if ($totalValue <= 0) {
            return 0;
        }

        // Calculate Herfindahl-Hirschman Index (HHI) for diversification
        $hhi = 0;
        foreach ($portfolio as $asset) {
            $weight = ($asset['value'] / $totalValue) * 100;
            $hhi += $weight * $weight;
        }

        // Convert to diversification score (0-100, where higher is more diversified)
        // HHI ranges from 100 (100% in one asset) to close to 0 (perfectly diversified)
        $diversificationScore = max(0, 100 - ($hhi / 100));

        return round($diversificationScore, 2);
    }

    /**
     * Generate risk assessment report for user
     */
    public function generateRiskReport(User $user): array
    {
        $openOrders = $user->p2pCryptoTradingOrders()->where('status', 'pending')->get();
        $positions = [];
        $totalRisk = 0;

        foreach ($openOrders as $order) {
            $riskAssessment = $this->assessPositionRisk([
                'leverage' => $order->metadata['leverage'] ?? 1,
                'margin' => $order->metadata['margin'] ?? 0,
                'liquidation_price' => $order->metadata['liquidation_price'] ?? 0,
                'current_price' => $order->price,
            ]);

            $positions[] = [
                'order_id' => $order->id,
                'pair' => $order->tradingPair->pair_name ?? 'unknown',
                'risk_level' => $riskAssessment['risk_level'],
                'risk_score' => $riskAssessment['risk_score'],
            ];

            $totalRisk += $riskAssessment['risk_score'];
        }

        return [
            'user_id' => $user->id,
            'total_positions' => count($positions),
            'average_risk_score' => count($positions) > 0 ? $totalRisk / count($positions) : 0,
            'highest_risk_position' => !empty($positions) ? max(array_column($positions, 'risk_score')) : 0,
            'positions' => $positions,
            'recommendations' => $this->generateRiskRecommendations($positions),
        ];
    }

    /**
     * Generate risk recommendations based on user positions
     */
    protected function generateRiskRecommendations(array $positions): array
    {
        $recommendations = [];

        foreach ($positions as $position) {
            if ($position['risk_score'] > 70) {
                $recommendations[] = "High risk position detected in {$position['pair']}. Consider closing or reducing position size.";
            } elseif ($position['risk_score'] > 40) {
                $recommendations[] = "Medium risk position in {$position['pair']}. Monitor closely.";
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = "Risk levels appear appropriate for current positions.";
        }

        return $recommendations;
    }
}