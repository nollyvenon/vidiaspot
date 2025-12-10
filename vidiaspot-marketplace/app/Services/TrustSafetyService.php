<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificationRecord;
use App\Models\TrustScore;
use App\Models\Report;
use App\Models\BuyerProtection;
use App\Models\InsuranceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TrustSafetyService
{
    /**
     * Initiate biometric verification for a user
     */
    public function initiateBiometricVerification($userId, $type = 'fingerprint', $subtype = null)
    {
        $user = User::findOrFail($userId);

        $verification = VerificationRecord::create([
            'user_id' => $userId,
            'verification_type' => $type,
            'verification_subtype' => $subtype ?? $type,
            'verification_metadata' => [
                'initiated_by' => $userId,
                'initiated_at' => now()->toISOString(),
                'initiated_from' => request()->ip(),
                'device_info' => request()->userAgent(),
            ],
            'status' => 'pending',
            'result' => 'pending',
            'verification_session_id' => 'VRS_' . strtoupper(Str::random(12)),
            'ip_address' => request()->ip(),
            'device_info' => ['user_agent' => request()->userAgent()],
        ]);

        // Trigger biometric capture in frontend
        return $verification;
    }

    /**
     * Process biometric verification data
     */
    public function processBiometricVerification($verificationId, $biometricData)
    {
        $verification = VerificationRecord::findOrFail($verificationId);

        // In a real implementation, this would connect to a biometric verification service
        // For this simulation, we'll generate a confidence score based on the data
        $confidence = mt_rand(70, 99);

        $verification->update([
            'verification_data' => encrypt(json_encode($biometricData)),
            'confidence_score' => $confidence,
            'result' => $confidence >= 80 ? 'success' : 'failed',
            'status' => $confidence >= 80 ? 'active' : 'inactive',
            'verified_at' => now(),
            'expires_at' => now()->addMonths(6), // Valid for 6 months
            'verification_metadata' => array_merge(
                $verification->verification_metadata ?? [],
                ['confidence_score' => $confidence, 'processed_at' => now()]
            ),
            'hash_verification' => hash('sha256', json_encode($biometricData)),
        ]);

        // Update user's verification status if successful
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
                'initiated_for_transaction' => $transactionId,
                'verification_purpose' => $purpose,
                'initiated_at' => now()->toISOString(),
                'initiated_from' => request()->ip(),
            ],
            'status' => 'pending',
            'result' => 'pending',
            'verification_session_id' => 'VID_' . strtoupper(Str::random(12)),
            'ip_address' => request()->ip(),
        ]);

        return $verification;
    }

    /**
     * Process video verification
     */
    public function processVideoVerification($verificationId, $videoPath, $metadata = [])
    {
        $verification = VerificationRecord::findOrFail($verificationId);

        // In a real implementation, this would use face recognition and identity verification services
        $confidence = mt_rand(75, 100); // Simulated confidence score

        $verification->update([
            'file_path' => $videoPath,
            'verification_metadata' => array_merge(
                $verification->verification_metadata ?? [],
                $metadata,
                ['confidence_score' => $confidence, 'processed_at' => now()]
            ),
            'confidence_score' => $confidence,
            'result' => $confidence >= 85 ? 'success' : 'failed',
            'status' => $confidence >= 85 ? 'active' : 'inactive',
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
    public function createReport($reporterUserId, $entityType, $entityId, $reportType, $description, $evidenceAttachments = [])
    {
        $report = Report::create([
            'reporter_user_id' => $reporterUserId,
            'reported_entity_type' => $entityType,
            'reported_entity_id' => $entityId,
            'report_type' => $reportType,
            'description' => $description,
            'evidence_attachments' => $evidenceAttachments,
            'status' => 'pending',
            'severity_level' => 'medium', // Will be adjusted by AI analysis
            'ai_analysis_results' => [
                'initial_severity' => 'medium',
                'flags' => [],
                'automated_decision' => null
            ],
        ]);

        // Simulate AI analysis which would happen in real implementation
        $analysis = $this->analyzeReportWithAI($report);

        $report->update([
            'ai_analysis_results' => $analysis,
            'severity_level' => $analysis['predicted_severity'] ?? 'medium',
            'manual_review_required' => $analysis['requires_manual_review'] ?? true,
        ]);

        // Update trust scores based on the report
        $this->updateRelatedTrustScores($report);

        return $report;
    }

    /**
     * AI analysis of reports (simulated)
     */
    public function analyzeReportWithAI($report)
    {
        // In real implementation, this would connect to AI/ML models
        $keywords = ['scam', 'fraud', 'fake', 'phishing', 'counterfeit', 'imposter', 'fake'];
        $severity = 'medium';
        
        // Check for keywords that might indicate high severity
        foreach ($keywords as $keyword) {
            if (stripos($report->description, $keyword) !== false) {
                $severity = 'high';
                break;
            }
        }

        // Check if there are multiple reports on same entity recently
        $recentReports = Report::where('reported_entity_type', $report->reported_entity_type)
                            ->where('reported_entity_id', $report->reported_entity_id)
                            ->where('created_at', '>', now()->subWeek())
                            ->count();

        if ($recentReports >= 3) {
            $severity = 'high';
        }

        return [
            'predicted_severity' => $severity,
            'requires_manual_review' => $severity === 'high',
            'confidence_score' => mt_rand(70, 95),
            'predicted_category' => $report->report_type,
            'automated_decision' => null, // Would be 'dismiss', 'verify', 'escalate', etc. in real impl
            'is_likely_spam' => false,
            'detected_patterns' => [],
        ];
    }

    /**
     * Get seller performance dashboard
     */
    public function getSellerPerformanceDashboard($userId)
    {
        $user = User::findOrFail($userId);
        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId], [
            'trust_score' => 50.00,
            'verification_level' => 'basic',
            'background_check_status' => 'pending',
        ]);

        // In a real implementation, we would gather actual metrics from transactions
        return [
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'registration_date' => $user->created_at,
                'account_age_months' => now()->diffInMonths($user->created_at),
            ],
            'trust_metrics' => [
                'overall_trust_score' => $trustScore->trust_score,
                'verification_level' => $trustScore->verification_level,
                'background_check_passed' => $trustScore->background_check_status === 'verified',
                'account_age_months' => $trustScore->account_age_months ?? now()->diffInMonths($user->created_at),
                'total_transactions' => $trustScore->total_transactions ?? 0,
                'dispute_rate' => $trustScore->total_transactions > 0 ? 
                                 min(100, ($trustScore->dispute_count / $trustScore->total_transactions) * 100) : 0,
                'positive_interaction_rate' => $trustScore->positive_interactions + $trustScore->negative_interactions > 0 ?
                                              ($trustScore->positive_interactions / ($trustScore->positive_interactions + $trustScore->negative_interactions)) * 100 : 0,
            ],
            'performance_indicators' => $trustScore->performance_indicators,
            'safety_indicators' => [
                'suspicious_activities' => $trustScore->suspicious_activity_count ?? 0,
                'complaints_received' => $trustScore->complaint_count ?? 0,
                'fraud_indicators' => $trustScore->fraud_indicators ?? [],
            ],
            'protection_eligibility' => [
                'buyer_protection' => $trustScore->protection_eligibility ?? true,
                'transaction_insurance' => $trustScore->insurance_eligibility ?? true,
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

        // Check if user is eligible for protection based on trust score
        if (!$trustScore->protection_eligibility) {
            throw new \Exception('User not eligible for buyer protection');
        }

        // Calculate premium based on transaction value and risk
        $transactionValue = $this->getTransactionValue($transactionId, $transactionType);
        $coverageAmount = $transactionValue * 1.1; // 110% of transaction value
        $premiumAmount = $transactionValue * 0.02; // 2% premium

        $protection = BuyerProtection::create([
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'transaction_type' => $transactionType,
            'transaction_reference' => $transactionReference,
            'provider_id' => $providerId,
            'policy_number' => 'VBP_' . date('Y') . '_' . strtoupper(Str::random(8)), // VidiAspot Buyer Protection policy number
            'coverage_amount' => $coverageAmount,
            'premium_amount' => $premiumAmount,
            'status' => 'active',
            'protection_type' => 'full_refund',
            'coverage_terms' => [
                'coverage_period' => '30_days',
                'refund_percentage' => '100%',
                'covered_issues' => ['damaged_goods', 'non_delivery', 'wrong_item', 'scam', 'non_receipt'],
                'exclusions' => ['change_of_mind', 'abuse'],
            ],
            'exclusions' => ['change_of_mind', 'abuse'],
            'claim_status' => 'no_claim',
            'purchase_date' => now(),
            'expiry_date' => now()->addYears(1),
            'renewal_date' => now()->addYears(1),
            'custom_fields' => [],
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

        if (!$protection->isValid()) {
            throw new \Exception('Protection is no longer valid');
        }

        $protection->update([
            'claim_amount' => $claimAmount,
            'claim_status' => 'pending',
            'claim_details' => [
                'reason' => $reason,
                'evidence' => $evidence,
                'submitted_at' => now(),
                'status' => 'pending_review',
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

        // In a real implementation, this would connect to a background check service
        // For simulation, we'll randomly determine the result
        $passed = mt_rand(1, 100) > 10; // 90% pass rate for demo

        $trustScore->update([
            'background_check_status' => $passed ? 'verified' : 'flagged',
            'background_check_details' => [
                'check_type' => $checkType,
                'performed_at' => now(),
                'result' => $passed ? 'passed' : 'flagged',
                'notes' => $passed ? 'No adverse findings' : 'Potential issues detected',
                'provider_used' => 'Demo Background Check Service',
                'raw_result' => $passed ? 'All clear' : 'Issues found',
            ],
        ]);

        return $trustScore;
    }

    /**
     * Get trust score for a user
     */
    public function getTrustScore($userId)
    {
        return TrustScore::firstOrCreate(['user_id' => $userId], [
            'trust_score' => 50.00,
            'verification_level' => 'basic',
            'background_check_status' => 'pending',
        ]);
    }

    /**
     * Update trust score based on activities
     */
    public function updateTrustScore($userId, $pointsChange, $activityType = null, $description = '')
    {
        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId], [
            'trust_score' => 50.00,
            'verification_level' => 'basic',
            'background_check_status' => 'pending',
        ]);

        // Update trust score with bounds checking
        $newScore = max(0, min(100, $trustScore->trust_score + $pointsChange));

        // Update metrics based on activity type
        $metrics = $trustScore->trust_metrics ?? [];
        if ($activityType) {
            $metrics[$activityType] = ($metrics[$activityType] ?? 0) + $pointsChange;
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
     * Get user's verification status
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
            'verification_details' => [],
        ];

        foreach ($verifications as $verification) {
            $status['verification_types'][] = $verification->verification_type;
            
            $status['verification_details'][] = [
                'type' => $verification->verification_type,
                'subtype' => $verification->verification_subtype,
                'confidence' => $verification->confidence_score,
                'verified_at' => $verification->verified_at,
                'expires_at' => $verification->expires_at,
                'status' => $verification->status,
            ];

            // Determine if any verification is active
            $status['is_verified'] = true;

            // Update highest level based on verification type
            if ($verification->verification_type === 'biometric' && $status['highest_verification'] !== 'biometric') {
                $status['highest_verification'] = 'biometric';
            } elseif ($verification->verification_type === 'video' && !in_array($status['highest_verification'], ['biometric'])) {
                $status['highest_verification'] = 'video';
            } elseif ($verification->verification_type === 'document' && $status['highest_verification'] === 'basic') {
                $status['highest_verification'] = 'document';
            }

            // Update earliest expiry date
            if ($verification->expires_at && (!$status['verification_expiry'] || $verification->expires_at->isBefore($status['verification_expiry']))) {
                $status['verification_expiry'] = $verification->expires_at;
            }
        }

        return $status;
    }

    /**
     * Get insurance providers by category and area
     */
    public function getInsuranceProviders($category = null, $area = null)
    {
        $query = InsuranceProvider::where('is_active', true);

        if ($category) {
            $query = $query->whereJsonContains('categories', $category);
        }

        if ($area) {
            $query = $query->whereJsonContains('coverage_areas', $area);
        }

        return $query->orderBy('rating', 'desc')
                    ->orderBy('claim_settlement_ratio', 'desc')
                    ->get();
    }

    /**
     * Calculate EMI for insurance premium payments
     */
    public function calculateEMI($principal, $interestRate, $tenure, $frequency = 'monthly')
    {
        $monthlyRate = $interestRate / 1200; // Monthly interest rate (annual rate / 12 / 100)
        
        switch ($frequency) {
            case 'monthly':
                $n = $tenure; // Number of months
                break;
            case 'quarterly':
                $n = $tenure * 4; // Number of quarters
                $monthlyRate = $interestRate / 400; // Quarterly rate
                break;
            case 'half-yearly':
                $n = $tenure * 2; // Number of half-years
                $monthlyRate = $interestRate / 200; // Half-yearly rate
                break;
            default:
                $n = $tenure;
        }
        
        // EMI calculation formula
        if ($monthlyRate > 0) {
            $emi = $principal * $monthlyRate * pow(1 + $monthlyRate, $n) / (pow(1 + $monthlyRate, $n) - 1);
        } else {
            $emi = $principal / $n; // Simple division if no interest
        }
        
        return [
            'emi_amount' => round($emi, 2),
            'total_amount' => round($emi * $n, 2),
            'total_interest' => round(($emi * $n) - $principal, 2),
            'frequency' => $frequency,
            'tenure_periods' => $n,
            'interest_rate' => $interestRate
        ];
    }

    /**
     * Compare insurance policies from different providers
     */
    public function compareInsurancePolicies($requirements, $category)
    {
        $providers = $this->getInsuranceProviders($category);

        $comparisons = [];

        foreach ($providers as $provider) {
            // Simulate comparison based on requirements
            $score = mt_rand(70, 98); // Simulated fitness score
            
            $comparisons[] = [
                'provider_id' => $provider->id,
                'provider_name' => $provider->name,
                'policy_name' => $provider->name . ' ' . ucfirst($category) . ' Insurance',
                'coverage_amount' => $requirements['coverage_amount'] ?? 'varies',
                'premium_amount' => $this->estimatePremium($provider, $requirements, $category),
                'features' => $provider->features ?? [],
                'claim_settlement_ratio' => $provider->claim_settlement_ratio ?? 'N/A',
                'rating' => $provider->rating ?? 0,
                'fit_score' => $score,
                'network_coverage' => $provider->coverage_areas ?? [],
            ];
        }

        // Sort by fit score
        usort($comparisons, function($a, $b) {
            return $b['fit_score'] <=> $a['fit_score'];
        });

        return $comparisons;
    }

    /**
     * Get user's insurance dashboard data
     */
    public function getUserInsuranceDashboard($userId)
    {
        $activePolicies = BuyerProtection::where('user_id', $userId)
                                       ->where('status', 'active')
                                       ->count();

        $expiringPolicies = BuyerProtection::where('user_id', $userId)
                                         ->where('status', 'active')
                                         ->where('expiry_date', '<=', now()->addDays(30))
                                         ->count();

        $totalCoverage = BuyerProtection::where('user_id', $userId)
                                       ->sum('coverage_amount');

        $totalPremium = BuyerProtection::where('user_id', $userId)
                                      ->sum('premium_amount');

        return [
            'total_policies' => $activePolicies,
            'expiring_policies' => $expiringPolicies,
            'total_coverage' => $totalCoverage,
            'total_premium_paid' => $totalPremium,
        ];
    }

    /**
     * Private helper methods
     */

    private function updateUserVerificationLevel($userId)
    {
        $verifications = VerificationRecord::where('user_id', $userId)
                                         ->where('status', 'active')
                                         ->where('result', 'success')
                                         ->pluck('verification_type')
                                         ->toArray();

        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId]);
        $currentLevel = $trustScore->verification_level;

        // Determine new verification level
        $newLevel = 'basic';
        if (in_array('biometric', $verifications)) {
            $newLevel = 'biometric';
        } elseif (in_array('video', $verifications)) {
            $newLevel = 'video_verified';
        } elseif (in_array('document', $verifications)) {
            $newLevel = 'document_verified';
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
            $impactPoints = $this->getSeverityImpact($report->severity_level);
            $this->updateTrustScore(
                $entityUserId, 
                -$impactPoints, 
                'reported', 
                "Reported for {$report->report_type}"
            );
        }

        // Update reporter's trust score if it's a valid report
        $validReportIncrease = 1; // Small increase for making valid reports
        $this->updateTrustScore(
            $report->reporter_user_id, 
            $validReportIncrease, 
            'report_made', 
            "Filed valid report"
        );
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
            case 'insurance_provider':
                $provider = \App\Models\InsuranceProvider::find($entityId);
                return $provider ? $provider->user_id : null; // Assuming providers are linked to users
            case 'food_vendor':
                $vendor = \App\Models\FoodVendor::find($entityId);
                return $vendor ? $vendor->user_id : null;
            case 'courier_partner':
                $partner = \App\Models\Logistics\CourierPartner::find($entityId);
                return $partner ? $partner->user_id : null;
            case 'location':
                $location = \App\Models\Location::find($entityId);
                return $location ? $location->user_id : null;
            default:
                return null;
        }
    }

    private function getSeverityImpact($severity)
    {
        switch ($severity) {
            case 'critical':
                return 15;
            case 'high':
                return 10;
            case 'medium':
                return 5;
            case 'low':
                return 2;
            default:
                return 5;
        }
    }

    private function getTransactionValue($transactionId, $transactionType)
    {
        // In real implementation, this would fetch the actual transaction value
        // For demo, return random values based on type
        switch ($transactionType) {
            case 'ad_purchase':
                return mt_rand(1000, 100000); // Varies depending on ad category
            case 'food_order':
                return mt_rand(500, 5000); // Food orders usually smaller
            case 'insurance_premium':
                return mt_rand(5000, 50000); // Insurance premiums
            case 'service_booking':
                return mt_rand(1000, 20000); // Service bookings
            default:
                return mt_rand(1000, 10000); // Default range
        }
    }

    private function estimatePremium($provider, $requirements, $category)
    {
        // In real implementation, this would connect to provider APIs
        // For demo, return estimated premiums
        $basePremium = $requirements['coverage_amount'] * 0.02; // 2% of coverage as baseline
        
        // Apply provider-specific adjustments
        $adjustment = mt_rand(80, 120) / 100; // Random adjustment between 0.8x and 1.2x
        
        return $basePremium * $adjustment;
    }

    private function updateVerificationLevelByScore($trustScore)
    {
        $score = $trustScore->trust_score;
        $currentLevel = $trustScore->verification_level;

        if ($score >= 90 && $currentLevel !== 'elite') {
            $trustScore->update(['verification_level' => 'elite']);
        } elseif ($score >= 80 && !in_array($currentLevel, ['elite'])) {
            $trustScore->update(['verification_level' => 'trusted']);
        } elseif ($score >= 70 && !in_array($currentLevel, ['elite', 'trusted'])) {
            $trustScore->update(['verification_level' => 'verified']);
        } elseif ($score >= 60 && in_array($currentLevel, ['basic'])) {
            $trustScore->update(['verification_level' => 'basic_plus']);
        }
    }
}