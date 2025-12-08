<?php

namespace App\Services;

use App\Models\VerificationRecord;
use App\Models\TrustScore;
use App\Models\Report;
use App\Models\BuyerProtection;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TrustSafetyService
{
    /**
     * Initiate biometric verification for a user
     */
    public function initiateBiometricVerification($userId, $type = 'fingerprint', $subtype = null)
    {
        $user = User::findOrFail($userId);

        // Create verification record
        $verification = VerificationRecord::create([
            'user_id' => $userId,
            'verification_type' => $type,
            'verification_subtype' => $subtype ?? $type,
            'verification_metadata' => ['initiated' => now()->toISOString()],
            'status' => 'pending',
            'result' => 'pending',
            'verification_session_id' => 'VRS_' . Str::random(16),
        ]);

        // Trigger biometric capture process
        $this->triggerBiometricCapture($verification->id);

        return $verification;
    }

    /**
     * Process biometric verification (simulated)
     */
    public function processBiometricVerification($verificationId, $biometricData)
    {
        $verification = VerificationRecord::findOrFail($verificationId);
        
        // Simulate biometric processing (in real app, this would connect to biometric processing service)
        $confidence = mt_rand(70, 99); // Generate random confidence score
        
        // Update verification record
        $verification->update([
            'verification_data' => encrypt(json_encode($biometricData)),
            'confidence_score' => $confidence,
            'result' => $confidence > 80 ? 'success' : 'failed',
            'status' => $confidence > 80 ? 'active' : 'inactive',
            'verified_at' => now(),
            'expires_at' => now()->addMonths(6), // Expires after 6 months
            'hash_verification' => hash('sha256', json_encode($biometricData)),
        ]);

        // Update user's verification level if successful
        if ($verification->result === 'success') {
            $this->updateUserVerificationLevel($verification->user_id);
        }

        return $verification;
    }

    /**
     * Initiate video verification for high-value transactions
     */
    public function initiateVideoVerification($userId, $transactionId = null, $purpose = 'identity_verification')
    {
        $user = User::findOrFail($userId);

        $verification = VerificationRecord::create([
            'user_id' => $userId,
            'verification_type' => 'video',
            'verification_subtype' => $purpose,
            'verification_metadata' => [
                'initiated_for' => $transactionId,
                'purpose' => $purpose,
                'initiated' => now()->toISOString()
            ],
            'status' => 'pending',
            'result' => 'pending',
            'verification_session_id' => 'VID_' . Str::random(16),
        ]);

        // In a real app, we'd trigger video recording process
        // For now, we'll simulate it being processed

        return $verification;
    }

    /**
     * Process video verification
     */
    public function processVideoVerification($verificationId, $videoPath, $metadata = [])
    {
        $verification = VerificationRecord::findOrFail($verificationId);

        // In a real app, this would use face recognition and identity verification APIs
        $confidence = mt_rand(75, 100); // Face recognition confidence
        
        $verification->update([
            'file_path' => $videoPath,
            'verification_metadata' => array_merge($verification->verification_metadata ?? [], $metadata),
            'confidence_score' => $confidence,
            'result' => $confidence > 85 ? 'success' : 'failed',
            'status' => $confidence > 85 ? 'active' : 'inactive',
            'verified_at' => now(),
            'expires_at' => now()->addMonths(3), // Video verification valid for 3 months
        ]);

        if ($verification->result === 'success') {
            $this->updateUserVerificationLevel($verification->user_id);
        }

        return $verification;
    }

    /**
     * Create a report for community monitoring
     */
    public function createReport($reporterUserId, $entityType, $entityId, $reportType, $description, $evidenceAttachements = [])
    {
        $report = Report::create([
            'reporter_user_id' => $reporterUserId,
            'reported_entity_type' => $entityType,
            'reported_entity_id' => $entityId,
            'report_type' => $reportType,
            'description' => $description,
            'evidence_attachments' => $evidenceAttachements,
            'status' => 'pending',
            'severity_level' => 'medium', // Will be adjusted by AI
        ]);

        // Run AI analysis on the report
        $aiAnalysis = $this->analyzeReportWithAI($report);
        
        // Update the report with AI analysis results
        $report->update([
            'ai_analysis_results' => $aiAnalysis,
            'severity_level' => $aiAnalysis['predicted_severity'] ?? 'medium',
            'manual_review_required' => $aiAnalysis['requires_manual_review'] ?? true,
        ]);

        // Update trust scores based on report
        $this->updateRelatedTrustScores($report);

        return $report;
    }

    /**
     * AI analysis of reports
     */
    public function analyzeReportWithAI($report)
    {
        // Simulated AI analysis - in real app, this would connect to ML service
        $keywords = ['scam', 'fraud', 'fake', 'phishing', 'spam'];
        $severity = 'medium';
        $requiresReview = true;
        
        foreach ($keywords as $keyword) {
            if (stripos($report->description, $keyword) !== false) {
                $severity = 'high';
                break;
            }
        }

        // Check for repeated reports on same entity
        $repeatCount = Report::where('reported_entity_type', $report->reported_entity_type)
                           ->where('reported_entity_id', $report->reported_entity_id)
                           ->count();

        if ($repeatCount >= 3) {
            $severity = 'high';
        }

        return [
            'predicted_severity' => $severity,
            'requires_manual_review' => $requiresReview,
            'confidence_score' => mt_rand(80, 95),
            'predicted_category' => $report->report_type,
            'automated_action' => null,
            'is_likely_spam' => false,
        ];
    }

    /**
     * Get seller performance dashboard
     */
    public function getSellerPerformanceDashboard($userId)
    {
        $trustScore = TrustScore::firstOrCreate([
            'user_id' => $userId
        ], [
            'trust_score' => 50.00,
            'verification_level' => 'basic',
            'background_check_status' => 'pending',
        ]);

        $user = User::find($userId);

        return [
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'registration_date' => $user->created_at,
            ],
            'trust_metrics' => [
                'overall_trust_score' => $trustScore->trust_score,
                'verification_level' => $trustScore->verification_level,
                'background_check_passed' => $trustScore->passedBackgroundCheck(),
                'account_age_months' => $trustScore->account_age_months,
                'total_transactions' => $trustScore->total_transactions,
                'dispute_rate' => $trustScore->total_transactions > 0 ? 
                                 ($trustScore->dispute_count / $trustScore->total_transactions) * 100 : 0,
                'positive_interaction_rate' => ($trustScore->positive_interactions / 
                                               max(1, $trustScore->positive_interactions + $trustScore->negative_interactions)) * 100,
            ],
            'performance_indicators' => $trustScore->performance_indicators,
            'safety_indicators' => [
                'suspicious_activities' => $trustScore->suspicious_activity_count,
                'complaints_received' => $trustScore->complaint_count,
                'fraud_indicators' => $trustScore->fraud_indicators,
            ],
            'protection_eligibility' => [
                'buyer_protection' => $trustScore->isEligibleForBuyerProtection(),
                'transaction_insurance' => $trustScore->isEligibleForInsurance(),
            ],
        ];
    }

    /**
     * Purchase buyer protection for a transaction
     */
    public function purchaseBuyerProtection($userId, $transactionId, $transactionType, $transactionReference, $providerId = null)
    {
        $user = User::findOrFail($userId);
        $trustScore = $this->getTrustScore($userId);

        // Check eligibility based on trust score
        if (!$trustScore->isEligibleForBuyerProtection()) {
            throw new \Exception('User not eligible for buyer protection');
        }

        // Determine coverage amount and premium based on transaction
        $transactionAmount = $this->getTransactionAmount($transactionId, $transactionType);
        $coverageAmount = $transactionAmount * 1.1; // 110% of transaction value
        $premiumAmount = $transactionAmount * 0.02; // 2% premium

        $protection = BuyerProtection::create([
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'transaction_type' => $transactionType,
            'transaction_reference' => $transactionReference,
            'provider_id' => $providerId, // Could be default platform provider
            'policy_number' => 'BP_' . date('Y') . '_' . strtoupper(Str::random(8)),
            'coverage_amount' => $coverageAmount,
            'premium_amount' => $premiumAmount,
            'status' => 'active',
            'protection_type' => 'full_refund', // Default protection type
            'coverage_terms' => [
                'return_period' => '30 days',
                'refund_percentage' => '100%',
                'covered_issues' => ['damaged_goods', 'non_delivery', 'wrong_item', 'scam'],
                'exclusions' => ['change_of_mind', 'abuse'],
            ],
            'exclusions' => ['change_of_mind', 'abuse'],
            'purchase_date' => now(),
            'expiry_date' => now()->addYears(1),
            'renewal_date' => now()->addYears(1),
        ]);

        return $protection;
    }

    /**
     * File a protection claim
     */
    public function fileProtectionClaim($protectionId, $claimAmount, $reason, $evidence = [])
    {
        $protection = BuyerProtection::findOrFail($protectionId);

        if ($protection->claim_status !== 'no_claim') {
            throw new \Exception('Claim already filed for this protection');
        }

        if (!$protection->isEligibleForClaim()) {
            throw new \Exception('Protection not eligible for claim');
        }

        $protection->update([
            'claim_amount' => $claimAmount,
            'claim_status' => 'pending',
            'claim_details' => [
                'reason' => $reason,
                'evidence' => $evidence,
                'submitted_date' => now(),
                'status' => 'pending review',
            ],
            'claim_date' => now(),
        ]);

        return $protection;
    }

    /**
     * Perform background check for service providers
     */
    public function performBackgroundCheck($userId, $checkType = 'standard')
    {
        $user = User::findOrFail($userId);
        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId]);

        // In a real app, this would connect to background check service
        // For simulation, we'll randomly determine the result
        $result = mt_rand(1, 100) > 10; // 90% chance of passing for demo

        $trustScore->update([
            'background_check_status' => $result ? 'verified' : 'flagged',
            'background_check_details' => [
                'check_type' => $checkType,
                'completed_at' => now(),
                'result' => $result ? 'cleared' : 'flagged',
                'details' => $result ? 'No issues found' : 'Potential issues detected',
                'provider_used' => 'Simulated Background Check Service',
            ],
        ]);

        return $trustScore;
    }

    /**
     * Get trust score for a user
     */
    public function getTrustScore($userId)
    {
        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId], [
            'trust_score' => 50.00,
            'verification_level' => 'basic',
            'background_check_status' => 'pending',
        ]);

        return $trustScore;
    }

    /**
     * Update trust score based on activities
     */
    public function updateTrustScore($userId, $scoreChange, $metric = null, $description = '')
    {
        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId], [
            'trust_score' => 50.00,
            'verification_level' => 'basic',
            'background_check_status' => 'pending',
        ]);

        // Calculate new trust score
        $newScore = max(0, min(100, $trustScore->trust_score + $scoreChange));

        // Update trust metrics
        $metrics = $trustScore->trust_metrics ?? [];
        if ($metric) {
            $metrics[$metric] = $metrics[$metric] ?? 0;
            $metrics[$metric] += $scoreChange;
        }

        $trustScore->update([
            'trust_score' => $newScore,
            'trust_metrics' => $metrics,
            'last_updated' => now(),
        ]);

        // Update verification level based on score
        $this->updateVerificationLevelByScore($trustScore);

        return $trustScore;
    }

    /**
     * Check user verification status
     */
    public function getUserVerificationStatus($userId)
    {
        $verifications = VerificationRecord::where('user_id', $userId)
                                          ->where('status', 'active')
                                          ->get();

        $status = [
            'is_verified' => false,
            'verification_types' => [],
            'highest_verification' => 'basic',
            'verification_expiry' => null,
        ];

        foreach ($verifications as $verification) {
            $status['verification_types'][] = $verification->verification_type;
            if ($verification->expires_at && (!$status['verification_expiry'] || $verification->expires_at < $status['verification_expiry'])) {
                $status['verification_expiry'] = $verification->expires_at;
            }
        }

        if (!empty($status['verification_types'])) {
            $status['is_verified'] = true;
            // Determine highest verification level
            if (in_array('biometric', $status['verification_types'])) {
                $status['highest_verification'] = 'biometric';
            } elseif (in_array('video', $status['verification_types'])) {
                $status['highest_verification'] = 'video';
            } elseif (in_array('document', $status['verification_types'])) {
                $status['highest_verification'] = 'document';
            }
        }

        return $status;
    }

    /**
     * Process a completed transaction to update trust scores
     */
    public function processTransactionForTrust($transactionId, $transactionType, $buyerId, $sellerId, $amount)
    {
        // Update buyer's trust score
        $this->updateTrustScore($buyerId, 1, 'transactions_completed', "Completed transaction #{$transactionId}");

        // Update seller's trust score
        $this->updateTrustScore($sellerId, 2, 'sales_completed', "Sold item in transaction #{$transactionId}");

        // Update transaction counts
        $this->updateTransactionCounts($buyerId, $sellerId);
    }

    /**
     * Private methods
     */

    private function triggerBiometricCapture($verificationId)
    {
        // In a real app, this would initiate the device's biometric scanner
        // For simulation purposes, we're just logging
        \Log::info("Biometric capture triggered for verification ID: {$verificationId}");
    }

    private function updateUserVerificationLevel($userId)
    {
        $verifications = VerificationRecord::where('user_id', $userId)
                                          ->where('status', 'active')
                                          ->where('result', 'success')
                                          ->get();

        $verificationTypes = $verifications->pluck('verification_type')->toArray();

        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId]);
        $currentLevel = $trustScore->verification_level;

        $newLevel = 'basic';
        if (in_array('biometric', $verificationTypes)) {
            $newLevel = 'trusted';
        } elseif (in_array('video', $verificationTypes)) {
            $newLevel = 'verified';
        } elseif (in_array('document', $verificationTypes)) {
            $newLevel = 'verified';
        }

        if ($newLevel !== $currentLevel) {
            $trustScore->update(['verification_level' => $newLevel]);
        }
    }

    private function updateRelatedTrustScores($report)
    {
        // Update trust scores for the reported entity
        $entityUserId = $this->getEntityUserId($report->reported_entity_type, $report->reported_entity_id);
        if ($entityUserId) {
            // Deduct points based on severity
            $pointImpact = $this->getSeverityImpact($report->severity_level);
            $this->updateTrustScore($entityUserId, -$pointImpact, 'report_received', "Reported for {$report->report_type}");
        }

        // Update reporter's trust score if it's a valid report
        $validReportIncrease = 1; // Small increase for valid reports
        $this->updateTrustScore($report->reporter_user_id, $validReportIncrease, 'report_made', "Filed valid report");
    }

    private function getEntityUserId($entityType, $entityId)
    {
        switch ($entityType) {
            case 'user':
                return $entityId;
            case 'ad':
                $ad = \App\Models\Ad::find($entityId);
                return $ad ? $ad->user_id : null;
            case 'vendor_store':
                $store = \App\Models\VendorStore::find($entityId);
                return $store ? $store->user_id : null;
            case 'food_vendor':
                $vendor = \App\Models\FoodVendor::find($entityId);
                return $vendor ? $vendor->user_id : null;
            default:
                return null;
        }
    }

    private function getSeverityImpact($severity)
    {
        switch ($severity) {
            case 'high':
            case 'critical':
                return 10;
            case 'medium':
                return 5;
            case 'low':
                return 2;
            default:
                return 5;
        }
    }

    private function getTransactionAmount($transactionId, $transactionType)
    {
        // In a real app, this would fetch the transaction amount from the appropriate table
        // For demo, we'll simulate different amounts
        switch ($transactionType) {
            case 'ad_purchase':
                return mt_rand(1000, 1000000); // Varies widely depending on ad
            case 'food_order':
                return mt_rand(1000, 20000); // Usually lower amounts
            case 'insurance_purchase':
                return mt_rand(10000, 100000); // Insurance premiums
            default:
                return mt_rand(5000, 100000); // Default range
        }
    }

    private function updateTransactionCounts($buyerId, $sellerId)
    {
        // Update buyer's transaction count
        $buyerTrust = TrustScore::firstOrCreate(['user_id' => $buyerId]);
        $buyerTrust->increment('total_transactions');
        $buyerTrust->update(['account_age_months' => now()->diffInMonths($buyerTrust->user->created_at)]);

        // Update seller's transaction count
        $sellerTrust = TrustScore::firstOrCreate(['user_id' => $sellerId]);
        $sellerTrust->increment('total_transactions');
        $sellerTrust->update(['account_age_months' => now()->diffInMonths($sellerTrust->user->created_at)]);
    }

    private function updateVerificationLevelByScore($trustScore)
    {
        $score = $trustScore->trust_score;
        $currentLevel = $trustScore->verification_level;

        if ($score >= 80 && $currentLevel !== 'elite') {
            $trustScore->update(['verification_level' => 'elite']);
        } elseif ($score >= 70 && $currentLevel !== 'trusted' && $currentLevel !== 'elite') {
            $trustScore->update(['verification_level' => 'trusted']);
        } elseif ($score >= 60 && !in_array($currentLevel, ['verified', 'trusted', 'elite'])) {
            $trustScore->update(['verification_level' => 'verified']);
        }
    }
}