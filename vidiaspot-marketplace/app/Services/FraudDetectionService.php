<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Ad;
use App\Models\Message;
use App\Models\Report;

class FraudDetectionService
{
    protected $suspiciousKeywords;
    protected $suspiciousPatterns;
    protected $riskThreshold;
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
        $this->suspiciousKeywords = [
            'free money', 'guaranteed loan', 'investment opportunity', 'urgent attention',
            'click here', 'act now', 'limited time', 'winner', 'congratulations',
            'paypal', 'bank transfer', 'wire transfer', 'western union', 'moneygram',
            'advance fee', 'inheritance', 'lottery', 'prize', 'refund', 'cash',
            'bitcoin', 'crypto', 'investment', 'profit', 'earn money', 'work from home'
        ];
        
        $this->suspiciousPatterns = [
            '/\b\d{3}-?\d{3}-?\d{4}\b/', // Phone numbers
            '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', // Email addresses
            '/https?:\/\/[^\s]+/', // URLs
            '/\$\d+|\d+\$/', // Money amounts
        ];
        
        $this->riskThreshold = 5; // Score threshold for flagging
    }

    /**
     * Analyze an ad for fraud risk
     *
     * @param Ad $ad
     * @return array
     */
    public function analyzeAdRisk(Ad $ad): array
    {
        $riskScore = 0;
        $flags = [];
        $details = [];

        // Check title for suspicious keywords
        $titleScore = $this->calculateKeywordScore($ad->title);
        $riskScore += $titleScore;
        if ($titleScore > 0) {
            $flags[] = 'suspicious_title';
            $details['title_keywords'] = $this->findSuspiciousKeywords($ad->title);
        }

        // Check description for suspicious keywords
        $descriptionScore = $this->calculateKeywordScore($ad->description);
        $riskScore += $descriptionScore;
        if ($descriptionScore > 0) {
            $flags[] = 'suspicious_description';
            $details['description_keywords'] = $this->findSuspiciousKeywords($ad->description);
        }

        // Check for excessive patterns (urls, emails, phones)
        $patternsScore = $this->analyzePatterns($ad->title . ' ' . $ad->description);
        $riskScore += $patternsScore;
        if ($patternsScore > 0) {
            $flags[] = 'excessive_patterns';
        }

        // Check price for anomalies
        $priceScore = $this->analyzePrice($ad);
        $riskScore += $priceScore;
        if ($priceScore > 0) {
            $flags[] = 'suspicious_price';
            $details['price_analysis'] = $this->getPriceAnomalyDetails($ad);
        }

        // Check user account age
        $userScore = $this->analyzeUserAccount($ad->user);
        $riskScore += $userScore;
        if ($userScore > 0) {
            $flags[] = 'new_account_risk';
        }

        // Check posting frequency
        $frequencyScore = $this->analyzePostingFrequency($ad->user_id);
        $riskScore += $frequencyScore;
        if ($frequencyScore > 0) {
            $flags[] = 'high_frequency_risk';
        }

        // Determine risk level
        $riskLevel = $this->getRiskLevel($riskScore);

        $result = [
            'ad_id' => $ad->id,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'is_suspicious' => $riskScore >= $this->riskThreshold,
            'flags' => $flags,
            'details' => $details,
        ];

        // Cache the result
        $cacheKey = "fraud_check:ad:{$ad->id}";
        $this->redisService->put($cacheKey, $result, 3600); // Cache for 1 hour

        return $result;
    }

    /**
     * Analyze a user account for fraud risk
     *
     * @param User $user
     * @return array
     */
    public function analyzeUserRisk(User $user): array
    {
        $riskScore = 0;
        $flags = [];
        $details = [];

        // Check account age
        $accountAgeScore = $this->analyzeAccountAge($user);
        $riskScore += $accountAgeScore;
        if ($accountAgeScore > 0) {
            $flags[] = 'new_account';
            $details['account_age_days'] = $user->created_at->diffInDays(now());
        }

        // Check previous violations
        $violationScore = $this->analyzeUserViolations($user->id);
        $riskScore += $violationScore;
        if ($violationScore > 0) {
            $flags[] = 'previous_violations';
            $details['violation_count'] = $violationScore;
        }

        // Check ad quality history
        $adQualityScore = $this->analyzeUserAdQuality($user->id);
        $riskScore += $adQualityScore;
        if ($adQualityScore > 0) {
            $flags[] = 'poor_ad_quality_history';
        }

        // Check user reports
        $reportScore = $this->analyzeUserReports($user->id);
        $riskScore += $reportScore;
        if ($reportScore > 0) {
            $flags[] = 'reported_by_others';
            $details['report_count'] = $reportScore;
        }

        // Check profile completeness
        $profileScore = $this->analyzeProfileCompleteness($user);
        $riskScore += $profileScore;
        if ($profileScore > 0) {
            $flags[] = 'incomplete_profile';
        }

        $riskLevel = $this->getRiskLevel($riskScore);

        return [
            'user_id' => $user->id,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'is_suspicious' => $riskScore >= $this->riskThreshold,
            'flags' => $flags,
            'details' => $details,
        ];
    }

    /**
     * Analyze a message for inappropriate content
     *
     * @param Message $message
     * @return array
     */
    public function analyzeMessageContent(Message $message): array
    {
        $riskScore = 0;
        $flags = [];
        $details = [];

        // Check content for suspicious keywords
        $contentScore = $this->calculateKeywordScore($message->content);
        $riskScore += $contentScore;
        if ($contentScore > 0) {
            $flags[] = 'suspicious_content';
            $details['content_keywords'] = $this->findSuspiciousKeywords($message->content);
        }

        // Check for patterns
        $patternsScore = $this->analyzePatterns($message->content);
        $riskScore += $patternsScore;
        if ($patternsScore > 0) {
            $flags[] = 'spam_patterns';
        }

        // Check user risk of sender
        $senderRisk = $this->analyzeUserRisk($message->sender);
        $riskScore += $senderRisk['risk_score'] * 0.3; // Lower weight for sender risk in messages

        $riskLevel = $this->getRiskLevel($riskScore);

        return [
            'message_id' => $message->id,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'is_suspicious' => $riskScore >= $this->riskThreshold,
            'flags' => $flags,
            'details' => $details,
        ];
    }

    /**
     * Calculate keyword-based risk score
     */
    private function calculateKeywordScore(string $text): int
    {
        $score = 0;
        $lowerText = strtolower($text);

        foreach ($this->suspiciousKeywords as $keyword) {
            if (strpos($lowerText, $keyword) !== false) {
                $score += 1;
            }
        }

        return $score;
    }

    /**
     * Find suspicious keywords in text
     */
    private function findSuspiciousKeywords(string $text): array
    {
        $keywords = [];
        $lowerText = strtolower($text);

        foreach ($this->suspiciousKeywords as $keyword) {
            if (strpos($lowerText, $keyword) !== false) {
                $keywords[] = $keyword;
            }
        }

        return $keywords;
    }

    /**
     * Analyze patterns in text
     */
    private function analyzePatterns(string $text): int
    {
        $score = 0;

        foreach ($this->suspiciousPatterns as $pattern) {
            preg_match_all($pattern, $text, $matches);
            if (count($matches[0]) > 2) { // More than 2 matches is suspicious
                $score += min(count($matches[0]), 3); // Cap at 3 points
            }
        }

        return $score;
    }

    /**
     * Analyze price anomalies
     */
    private function analyzePrice(Ad $ad): int
    {
        if (!$ad->price) {
            return 0; // No price to analyze
        }

        // Check if price is significantly lower than category average
        $categoryAvg = $this->getCategoryAveragePrice($ad->category_id);
        if ($categoryAvg > 0) {
            $priceRatio = $ad->price / $categoryAvg;
            
            // Flag if significantly higher or lower than average
            if ($priceRatio > 5 || $priceRatio < 0.1) {
                return 2;
            }
        }

        // Check for suspicious price patterns (like all nines: $999, $1999, etc.)
        $priceStr = (string)$ad->price;
        if (preg_match('/\d*[9][9]/', $priceStr)) {
            return 1;
        }

        return 0;
    }

    /**
     * Get category average price
     */
    private function getCategoryAveragePrice(int $categoryId): float
    {
        $cacheKey = "category_avg_price:{$categoryId}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        $avgPrice = DB::table('ads')
                     ->where('category_id', $categoryId)
                     ->where('status', 'active')
                     ->avg('price') ?? 0;

        $this->redisService->put($cacheKey, floatval($avgPrice), 3600 * 24); // Cache for 24 hours

        return floatval($avgPrice);
    }

    /**
     * Get price anomaly details
     */
    private function getPriceAnomalyDetails(Ad $ad): array
    {
        $categoryAvg = $this->getCategoryAveragePrice($ad->category_id);
        $ratio = $categoryAvg > 0 ? $ad->price / $categoryAvg : 0;

        return [
            'actual_price' => $ad->price,
            'category_average' => $categoryAvg,
            'ratio_to_average' => round($ratio, 2),
            'anomaly_type' => $ratio > 5 ? 'significantly_higher' : 'significantly_lower',
        ];
    }

    /**
     * Analyze user account risk based on account age
     */
    private function analyzeAccountAge(User $user): int
    {
        $ageInDays = $user->created_at->diffInDays(now());

        // Very new accounts (less than 7 days) are higher risk
        if ($ageInDays < 7) {
            return 2;
        }

        // New accounts (less than 30 days) are medium risk
        if ($ageInDays < 30) {
            return 1;
        }

        return 0;
    }

    /**
     * Analyze posting frequency
     */
    private function analyzePostingFrequency(int $userId): int
    {
        $recentAdsCount = DB::table('ads')
                           ->where('user_id', $userId)
                           ->where('created_at', '>', now()->subHours(24))
                           ->count();

        // More than 5 ads in 24 hours is suspicious
        if ($recentAdsCount > 5) {
            return min($recentAdsCount - 3, 5); // Scale up to max 5 points
        }

        return 0;
    }

    /**
     * Analyze user account for fraud risk
     */
    private function analyzeUserAccount(User $user): int
    {
        $score = 0;

        // Check if email is from disposable service
        if ($this->isDisposableEmail($user->email)) {
            $score += 2;
        }

        // Check profile completeness
        $completenessScore = $this->analyzeProfileCompleteness($user);
        $score += $completenessScore;

        return $score;
    }

    /**
     * Check if email is disposable
     */
    private function isDisposableEmail(string $email): bool
    {
        $disposableDomains = [
            '10minutemail.com', 'guerrillamail.com', 'tempmail.org',
            'mailinator.com', 'sharklasers.com', 'throwawaymail.com'
        ];

        $domain = substr(strrchr($email, "@"), 1);
        return in_array(strtolower($domain), array_map('strtolower', $disposableDomains));
    }

    /**
     * Analyze profile completeness
     */
    private function analyzeProfileCompleteness(User $user): int
    {
        $score = 0;
        $totalFields = 5; // name, email, phone, address, avatar
        $completeFields = 0;

        if (!empty($user->name)) $completeFields++;
        if (!empty($user->email)) $completeFields++;
        if (!empty($user->phone)) $completeFields++;
        if (!empty($user->address)) $completeFields++;
        if (!empty($user->avatar)) $completeFields++;

        $completenessRatio = $completeFields / $totalFields;

        // Low completeness score increases risk
        if ($completenessRatio < 0.4) {
            $score += 2;
        } elseif ($completenessRatio < 0.7) {
            $score += 1;
        }

        return $score;
    }

    /**
     * Analyze user violations
     */
    private function analyzeUserViolations(int $userId): int
    {
        // This would check for previous violations in a violations table
        // For now, we'll simulate by checking user reports
        return Report::where('user_id', $userId)->count();
    }

    /**
     * Analyze user ad quality history
     */
    private function analyzeUserAdQuality(int $userId): int
    {
        $ads = Ad::where('user_id', $userId)->get();
        $lowQualityCount = 0;

        foreach ($ads as $ad) {
            $fraudCheck = $this->getCachedFraudCheck($ad->id);
            if ($fraudCheck && $fraudCheck['is_suspicious']) {
                $lowQualityCount++;
            }
        }

        return min($lowQualityCount, 3); // Cap at 3 points
    }

    /**
     * Analyze user reports
     */
    private function analyzeUserReports(int $userId): int
    {
        return Report::where('reported_user_id', $userId)
                    ->orWhereHas('ad', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    })
                    ->count();
    }

    /**
     * Get risk level based on score
     */
    private function getRiskLevel(int $score): string
    {
        if ($score >= $this->riskThreshold * 2) {
            return 'high';
        } elseif ($score >= $this->riskThreshold) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get cached fraud check result
     */
    private function getCachedFraudCheck(int $adId)
    {
        $cacheKey = "fraud_check:ad:{$adId}";
        return $this->redisService->get($cacheKey);
    }

    /**
     * Flag content as suspicious
     */
    public function flagContent(string $contentType, int $contentId, array $reasons = []): bool
    {
        $flaggedContent = DB::table('fraud_flags')->insert([
            'content_type' => $contentType,
            'content_id' => $contentId,
            'reasons' => json_encode($reasons),
            'flagged_at' => now(),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'status' => 'pending',
        ]);

        Log::info("Content flagged: {$contentType} #{$contentId}", $reasons);
        return $flaggedContent;
    }

    /**
     * Review flagged content
     */
    public function reviewFlaggedContent(int $flagId, string $action, int $reviewerId): bool
    {
        $update = [
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
            'status' => $action,
        ];

        $result = DB::table('fraud_flags')
                   ->where('id', $flagId)
                   ->update($update);

        return $result > 0;
    }

    /**
     * Get risk summary for admin dashboard
     */
    public function getRiskSummary(): array
    {
        $totalFlags = DB::table('fraud_flags')->count();
        $pendingFlags = DB::table('fraud_flags')->where('status', 'pending')->count();
        $highRiskUsers = DB::table('users')
                          ->join('ads', 'users.id', '=', 'ads.user_id')
                          ->join('fraud_flags', function ($join) {
                              $join->on('ads.id', '=', 'fraud_flags.content_id')
                                   ->where('fraud_flags.content_type', 'ad');
                          })
                          ->distinct('users.id')
                          ->count();

        return [
            'total_flags' => $totalFlags,
            'pending_reviews' => $pendingFlags,
            'high_risk_users' => $highRiskUsers,
            'active_monitors' => count($this->suspiciousKeywords) + count($this->suspiciousPatterns),
        ];
    }
}