<?php

namespace App\Services;

use App\Models\DemandForecast;
use App\Models\PricingRecommendation;
use App\Models\SuccessPrediction;
use App\Models\DuplicateDetection;
use App\Models\FraudDetection;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * AI Service - Main service that powers all AI-based features
 */
class AIService
{
    protected $openAiApiKey;
    protected $huggingFaceApiKey;

    public function __construct()
    {
        $this->openAiApiKey = config('services.openai.api_key');
        $this->huggingFaceApiKey = config('services.huggingface.api_key');
    }

    /**
     * Generate demand forecast for a category in a location
     */
    public function generateDemandForecast($categoryId, $locationId = null, $timePeriod = 'monthly')
    {
        try {
            // Simulate AI demand forecasting
            // In real implementation, this would use historical data and ML models
            $historicalData = $this->getHistoricalDemandData($categoryId, $locationId, $timePeriod);
            
            // Calculate demand forecast based on historical patterns, seasonality, and market trends
            $forecastData = $this->analyzeHistoricalPatterns($historicalData);
            
            // Determine confidence level based on data availability
            $dataPoints = count($historicalData);
            $confidenceLevel = min(100, ($dataPoints / 10) * 100); // Max 100% confidence
            
            // Factors that affect demand
            $factors = [
                'seasonal_trends' => $this->analyzeSeasonalTrends($categoryId),
                'economic_indicators' => $this->getEconomicIndicators(),
                'competitive_analysis' => $this->getCompetitiveAnalysis($categoryId, $locationId),
                'marketing_events' => $this->getUpcomingMarketingEvents($categoryId),
            ];

            $forecast = DemandForecast::create([
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'date_range' => $timePeriod,
                'forecast_date' => Carbon::now(),
                'predicted_demand' => $forecastData['predicted_demand'],
                'actual_demand' => null, // Will be filled later when actual data is available
                'confidence_level' => round($confidenceLevel, 2),
                'forecast_data' => $forecastData,
                'factors' => $factors,
            ]);

            return $forecast;
        } catch (\Exception $e) {
            Log::error('Error generating demand forecast: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate dynamic pricing recommendations for an ad
     */
    public function generatePricingRecommendation($ad, $categoryId = null, $locationId = null)
    {
        try {
            $categoryId = $categoryId ?: $ad->category_id;
            $locationId = $locationId ?: $ad->location_id;
            
            // Get current market prices for similar ads
            $marketPrices = $this->getMarketPrices($categoryId, $locationId, $ad->condition ?? 'any');
            
            // Calculate recommended price based on market analysis
            $recommendedPrice = $this->calculateOptimalPrice($ad, $marketPrices);
            
            // Get market averages and ranges
            $marketAverage = $marketPrices['average'] ?? $ad->price;
            $minPrice = $marketPrices['min'] ?? $ad->price * 0.5;
            $maxPrice = $marketPrices['max'] ?? $ad->price * 1.5;
            
            // Calculate confidence level based on market data availability
            $confidenceLevel = $marketPrices['confidence'] ?? 75.0;
            
            // Determine pricing strategy
            $pricingStrategy = $this->determinePricingStrategy($ad, $recommendedPrice, $marketAverage);
            
            // Factors affecting the pricing decision
            $factors = [
                'similar_ad_comparisons' => $marketPrices['comparisons'] ?? [],
                'supply_demand_ratio' => $this->calculateSupplyDemandRatio($categoryId, $locationId),
                'seasonal_adjustments' => $this->getSeasonalAdjustments($categoryId),
                'condition_based_adjustments' => $this->getConditionAdjustments($ad->condition ?? 'used'),
                'urgency_based_adjustments' => $this->getUrgencyAdjustments($ad),
            ];
            
            // Market trends affecting pricing
            $marketTrends = [
                'price_movement_direction' => $this->getPriceMovementDirection($categoryId, $locationId),
                'seasonal_trends' => $this->getSeasonalPricingTrends($categoryId),
                'competitor_pricing_changes' => $this->getCompetitorPricingChanges($categoryId, $locationId),
            ];

            $recommendation = PricingRecommendation::create([
                'ad_id' => $ad->id,
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'current_price' => $ad->price,
                'recommended_price' => $recommendedPrice,
                'market_average' => $marketAverage,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'confidence_level' => round($confidenceLevel, 2),
                'pricing_strategy' => $pricingStrategy,
                'analysis_data' => $factors,
                'market_trends' => $marketTrends,
                'reasoning' => $this->generatePricingReasoning($ad, $recommendedPrice, $marketAverage),
                'is_optimal' => abs($recommendedPrice - $marketAverage) <= ($marketAverage * 0.1), // Within 10% of market avg
                'expires_at' => Carbon::now()->addDays(7),
            ]);

            return $recommendation;
        } catch (\Exception $e) {
            Log::error('Error generating pricing recommendation: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate success probability and recommendations for an ad
     */
    public function generateSuccessPrediction($ad, $categoryId = null, $locationId = null)
    {
        try {
            $categoryId = $categoryId ?: $ad->category_id;
            $locationId = $locationId ?: $ad->location_id;
            
            // Analyze various factors that affect ad success
            $successFactors = $this->analyzeSuccessFactors($ad, $categoryId, $locationId);
            
            // Calculate success probability based on factors
            $successProbability = $this->calculateSuccessProbability($successFactors);
            
            // Generate predicted metrics
            $predictedMetrics = [
                'estimated_views' => $this->estimateViews($successFactors),
                'estimated_responses' => $this->estimateResponses($successFactors),
                'estimated_conversion_rate' => $this->estimateConversionRate($successFactors),
                'estimated_time_to_first_response' => rand(1, 48), // Hours
                'estimated_time_to_sale' => rand(1, 30), // Days
            ];
            
            // Identify risk factors
            $riskFactors = $this->identifyRiskFactors($ad, $successFactors);
            
            // Get comparative analysis with similar successful ads
            $comparativeAnalysis = $this->getComparativeAnalysis($ad, $categoryId, $locationId);
            
            // Generate improvement suggestions
            $improvementSuggestions = $this->generateImprovementSuggestions($ad, $successFactors);
            
            // Calculate engagement score
            $engagementScore = $this->calculateEngagementScore($ad->title, $ad->description, $ad->images);
            
            // Calculate conversion probability
            $conversionProbability = $this->calculateConversionProbability($ad->price, $successFactors['market_position']);
            
            $prediction = SuccessPrediction::create([
                'ad_id' => $ad->id,
                'user_id' => $ad->user_id,
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'ad_type' => $ad->type ?? 'product',
                'success_probability' => round($successProbability, 2),
                'success_factors' => $successFactors,
                'improvement_suggestions' => $improvementSuggestions,
                'predicted_metrics' => $predictedMetrics,
                'risk_factors' => $riskFactors,
                'confidence_level' => 'high', // Based on data completeness
                'predicted_duration' => $predictedMetrics['estimated_time_to_sale'],
                'engagement_score' => round($engagementScore, 2),
                'conversion_probability' => round($conversionProbability, 2),
                'optimization_tips' => $this->generateOptimizationTips($ad, $successFactors),
                'comparative_analysis' => $comparativeAnalysis,
                'prediction_generated_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(30),
            ]);

            return $prediction;
        } catch (\Exception $e) {
            Log::error('Error generating success prediction: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Detect potential duplicate ads
     */
    public function detectDuplicates($ad)
    {
        try {
            // Find similar ads based on title, description, price, location, and images
            $similarAds = $this->findSimilarAds($ad);

            $duplicateMatches = [];
            
            foreach ($similarAds as $similarAd) {
                // Calculate similarity score based on multiple factors
                $similarityScore = $this->calculateSimilarityScore($ad, $similarAd);
                
                if ($similarityScore >= 80) { // Threshold for considering as potential duplicate
                    // Identify matching attributes
                    $matchingAttributes = $this->compareAdAttributes($ad, $similarAd);
                    
                    // Analyze image similarity if both ads have images
                    $imageSimilarityData = $this->analyzeImageSimilarity($ad, $similarAd);
                    
                    // Analyze text similarity
                    $textSimilarityData = $this->analyzeTextSimilarity($ad, $similarAd);
                    
                    // Determine detection method
                    $detectionMethod = $imageSimilarityData['score'] > 85 ? 'image_matching' : 
                                     ($textSimilarityData['similarity'] > 85 ? 'text_analysis' : 'combined');
                    
                    // Generate reasoning for flagging as potential duplicate
                    $reasoning = $this->generateDuplicateReasoning($ad, $similarAd, $similarityScore);
                    
                    // Identify confidence factors
                    $confidenceFactors = [
                        'price_similarity' => $this->calculatePriceSimilarity($ad, $similarAd),
                        'location_similarity' => $this->calculateLocationSimilarity($ad, $similarAd),
                        'attribute_similarity' => $matchingAttributes,
                        'image_match_strength' => $imageSimilarityData['confidence'],
                        'text_match_strength' => $textSimilarityData['confidence'],
                    ];
                    
                    // Recommend action
                    $recommendedAction = [
                        'primary' => $similarityScore > 95 ? 'confirm_duplicate' : 
                                    ($similarityScore > 85 ? 'review_required' : 'likely_duplicate'),
                        'secondary' => 'compare_phone_numbers',
                        'tertiary' => 'check_profiles',
                    ];
                    
                    $duplicateDetection = DuplicateDetection::create([
                        'primary_ad_id' => $ad->id,
                        'duplicate_ad_id' => $similarAd->id,
                        'user_id' => $ad->user_id,
                        'category_id' => $ad->category_id,
                        'similarity_score' => round($similarityScore, 2),
                        'matching_attributes' => $matchingAttributes,
                        'image_similarity_data' => $imageSimilarityData,
                        'text_similarity_data' => $textSimilarityData,
                        'detection_method' => $detectionMethod,
                        'reasoning' => $reasoning,
                        'status' => 'flagged',
                        'detected_at' => Carbon::now(),
                        'confidence_factors' => $confidenceFactors,
                        'recommended_action' => $recommendedAction,
                        'is_confirmed_duplicate' => $similarityScore > 95,
                    ]);
                    
                    $duplicateMatches[] = $duplicateDetection;
                }
            }

            return $duplicateMatches;
        } catch (\Exception $e) {
            Log::error('Error detecting duplicates: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Detect potential fraudulent activities
     */
    public function detectFraud($ad = null, $user = null, $payment = null)
    {
        try {
            $flags = [];
            $riskScore = 0;
            $indicators = [];
            $behavioralPatterns = [];
            $suspiciousActivities = [];

            // If checking a user
            if ($user) {
                $userFlags = $this->analyzeUserBehavior($user);
                $riskScore += $userFlags['risk_score'];
                $indicators = array_merge($indicators, $userFlags['indicators']);
                $behavioralPatterns = array_merge($behavioralPatterns, $userFlags['behavioral_patterns']);
            }

            // If checking an ad
            if ($ad) {
                $adFlags = $this->analyzeAdContent($ad);
                $riskScore += $adFlags['risk_score'];
                $indicators = array_merge($indicators, $adFlags['indicators']);
                $suspiciousActivities = array_merge($suspiciousActivities, $adFlags['suspicious_activities']);
            }

            // If checking a payment
            if ($payment) {
                $paymentFlags = $this->analyzePayment($payment);
                $riskScore += $paymentFlags['risk_score'];
                $indicators = array_merge($indicators, $paymentFlags['indicators']);
                $suspiciousActivities = array_merge($suspiciousActivities, $paymentFlags['suspicious_activities']);
            }

            // Check for common fraud patterns
            $patternFlags = $this->checkCommonFraudPatterns($ad, $user, $payment);
            $riskScore += $patternFlags['risk_score'];
            $indicators = array_merge($indicators, $patternFlags['indicators']);
            $suspiciousActivities = array_merge($suspiciousActivities, $patternFlags['suspicious_activities']);

            // Normalize risk score to 0-100 range
            $riskScore = min(100, max(0, $riskScore));

            // Determine severity based on risk score
            $severity = match (true) {
                $riskScore >= 80 => 'critical',
                $riskScore >= 60 => 'high',
                $riskScore >= 40 => 'medium',
                default => 'low',
            };

            // Determine type of fraud
            $type = $this->determineFraudType($indicators);

            // Generate analysis details
            $analysisDetails = $this->generateFraudAnalysis($indicators, $behavioralPatterns, $suspiciousActivities);

            // Identify confidence factors
            $confidenceFactors = [
                'user_behavior' => $this->calculateUserBehaviorConfidence($user),
                'content_analysis' => $this->calculateContentAnalysisConfidence($ad),
                'payment_indicators' => $this->calculatePaymentIndicatorConfidence($payment),
                'pattern_matching' => $this->calculatePatternMatchingConfidence($patternFlags),
            ];

            // Recommend actions
            $recommendedActions = $this->generateRecommendedActions($riskScore, $type);

            // Determine affected resources
            $affectedResources = [
                'user_profile' => $user ? ['id' => $user->id, 'email' => $user->email] : null,
                'ad_listing' => $ad ? ['id' => $ad->id, 'title' => $ad->title] : null,
                'payment_transaction' => $payment ? ['id' => $payment->id, 'amount' => $payment->amount] : null,
            ];

            // Create fraud detection record
            $fraudDetection = FraudDetection::create([
                'user_id' => $user?->id,
                'ad_id' => $ad?->id,
                'payment_transaction_id' => $payment?->id,
                'type' => $type,
                'severity' => $severity,
                'risk_score' => round($riskScore, 2),
                'indicators' => $indicators,
                'behavioral_patterns' => $behavioralPatterns,
                'suspicious_activities' => $suspiciousActivities,
                'analysis_details' => $analysisDetails,
                'status' => $riskScore > 70 ? 'pending_review' : 'investigated',
                'confidence_factors' => $confidenceFactors,
                'recommended_actions' => $recommendedActions,
                'affected_resources' => $affectedResources,
                'detected_at' => Carbon::now(),
            ]);

            return $fraudDetection;
        } catch (\Exception $e) {
            Log::error('Error detecting fraud: ' . $e->getMessage());
            return null;
        }
    }

    // Helper methods for AI analysis
    
    private function getHistoricalDemandData($categoryId, $locationId, $timePeriod) {
        // Simulate getting historical demand data
        // In real implementation, this would query the database for historical ad success data
        $data = [];
        
        // This is a simplified simulation - in reality, this would use more sophisticated data analysis
        for ($i = 0; $i < 12; $i++) {
            $data[] = [
                'period' => Carbon::now()->subMonths($i)->format('Y-m'),
                'demand' => rand(100, 1000),
                'seasonal_factor' => rand(80, 120) / 100,
                'growth_rate' => rand(95, 105) / 100,
            ];
        }
        
        return $data;
    }
    
    private function analyzeHistoricalPatterns($historicalData) {
        // Analyze historical patterns to predict future demand
        $totalDemand = array_sum(array_column($historicalData, 'demand'));
        $avgDemand = $totalDemand / count($historicalData);
        
        // Calculate trend based on last few periods
        $recentData = array_slice($historicalData, 0, 3);
        $recentAvg = array_sum(array_column($recentData, 'demand')) / count($recentData);
        
        // Predict next period demand
        $growthRate = $avgDemand > 0 ? $recentAvg / $avgDemand : 1;
        $predictedDemand = (int)($avgDemand * $growthRate);
        
        return [
            'predicted_demand' => $predictedDemand,
            'historical_average' => $avgDemand,
            'growth_trend' => $growthRate > 1 ? 'increasing' : ($growthRate < 1 ? 'decreasing' : 'stable'),
            'seasonal_pattern' => $this->extractSeasonalPattern($historicalData),
        ];
    }
    
    private function analyzeSeasonalTrends($categoryId) {
        // Analyze seasonal trends for the specific category
        // This would normally use historical seasonal data
        $seasons = [
            'electronics' => ['Q4' => 95, 'Q1' => 60, 'Q2' => 40, 'Q3' => 70], // Holiday gift season
            'fashion' => ['Q4' => 90, 'Q1' => 50, 'Q2' => 60, 'Q3' => 75], // New year, summer
            'automotive' => ['Q1' => 80, 'Q2' => 90, 'Q3' => 85, 'Q4' => 70], // New model releases
            'property' => ['Q1' => 60, 'Q2' => 90, 'Q3' => 85, 'Q4' => 70], // Moving seasons
        ];
        
        $category = strtolower(Category::find($categoryId)?->name ?? 'general');
        
        return $seasons[$category] ?? ['Q1' => 70, 'Q2' => 80, 'Q3' => 75, 'Q4' => 85];
    }
    
    private function getEconomicIndicators() {
        // Return simulated economic indicators
        return [
            'inflation_rate' => rand(800, 2000) / 100, // 8-20%
            'unemployment_rate' => rand(400, 1200) / 100, // 4-12%
            'consumer_confidence' => rand(90, 110), // Base 100
            'currency_stability' => rand(80, 100), // Percentage
        ];
    }

    private function getMarketPrices($categoryId, $locationId = null, $condition = 'any') {
        // Get market prices for similar ads
        $query = Ad::where('category_id', $categoryId)
                  ->where('status', 'active')
                  ->where('price', '>', 0);

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        if ($condition !== 'any') {
            $query->where('condition', $condition);
        }

        $ads = $query->get();

        if ($ads->isEmpty()) {
            return [
                'average' => 0,
                'min' => 0,
                'max' => 0,
                'confidence' => 0,
                'comparisons' => [],
            ];
        }

        $prices = $ads->pluck('price')->toArray();
        $avg = array_sum($prices) / count($prices);
        $min = min($prices);
        $max = max($prices);

        // Calculate confidence based on number of comparisons
        $confidence = min(100, (count($prices) / 5) * 100); // Max 100% confidence with 5+ comparisons

        return [
            'average' => $avg,
            'min' => $min,
            'max' => $max,
            'confidence' => $confidence,
            'comparisons' => $ads->take(5)->map(function($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'price' => $ad->price,
                    'views_count' => $ad->views_count ?? 0,
                    'inquiries_count' => $ad->inquiries_count ?? 0,
                ];
            })->toArray(),
        ];
    }
    
    private function getCompetitiveAnalysis($categoryId, $locationId) {
        // Get competitive analysis for the category in the location
        $activeAds = Ad::where('category_id', $categoryId)
                  ->when($locationId, function($query) use ($locationId) {
                      return $query->where('location_id', $locationId);
                  })
                  ->count();
                  
        $avgPrice = Ad::where('category_id', $categoryId)
                 ->when($locationId, function($query) use ($locationId) {
                     return $query->where('location_id', $locationId);
                 })
                 ->avg('price') ?? 0;
                 
        return [
            'active_listings' => $activeAds,
            'market_saturation' => $activeAds > 100 ? 'high' : ($activeAds > 50 ? 'medium' : 'low'),
            'average_price' => $avgPrice,
            'competition_level' => $activeAds > 100 ? 'high' : ($activeAds > 50 ? 'medium' : 'low'),
        ];
    }
    
    private function getUpcomingMarketingEvents($categoryId) {
        // Return upcoming marketing events that might affect demand
        return [
            'sale_seasons' => ['black_friday', 'boxing_day', 'new_year'],
            'industry_events' => [],
            'local_holidays' => [],
        ];
    }
    
    private function getMarketPrices($categoryId, $locationId, $condition) {
        // Get market prices for similar ads
        $query = Ad::where('category_id', $categoryId)
                  ->where('status', 'active')
                  ->where('price', '>', 0);
                  
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        $ads = $query->get();
        
        if ($ads->isEmpty()) {
            return [
                'average' => 0,
                'min' => 0,
                'max' => 0,
                'confidence' => 0,
                'comparisons' => [],
            ];
        }
        
        $prices = $ads->pluck('price')->toArray();
        $average = array_sum($prices) / count($prices);
        $min = min($prices);
        $max = max($prices);
        
        // Calculate confidence based on number of comparisons
        $confidence = min(100, (count($prices) / 5) * 100); // Max 100% confidence with 5+ comparisons
        
        return [
            'average' => $average,
            'min' => $min,
            'max' => $max,
            'confidence' => $confidence,
            'comparisons' => $ads->take(5)->map(function($ad) {
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'price' => $ad->price,
                    'views' => $ad->views_count ?? 0,
                    'responses' => $ad->inquiries_count ?? 0,
                ];
            }),
        ];
    }
    
    private function calculateOptimalPrice($ad, $marketPrices) {
        // Calculate optimal price based on market data and ad-specific factors
        if (empty($marketPrices) || $marketPrices['average'] == 0) {
            return $ad->price ?? 0; // Use current price if no market data
        }

        $basePrice = $marketPrices['average'];
        $optimalPrice = $basePrice;

        // Adjust based on ad condition
        if (isset($ad->condition)) {
            $condition = strtolower($ad->condition);
            if ($condition === 'new') {
                $optimalPrice *= 1.15; // New items can command higher prices
            } elseif ($condition === 'excellent' || $condition === 'like_new') {
                $optimalPrice *= 1.05;
            } elseif ($condition === 'good') {
                $optimalPrice *= 0.95;
            } elseif ($condition === 'fair' || $condition === 'acceptable') {
                $optimalPrice *= 0.85;
            } elseif ($condition === 'poor' || $condition === 'damaged') {
                $optimalPrice *= 0.70;
            }
        }

        // Adjust based on ad quality factors
        $imageCount = is_array($ad->images) ? count($ad->images) : (isset($ad->images) ? 1 : 0);
        if ($imageCount > 3) {
            $optimalPrice *= 1.05; // More images increase value
        } elseif ($imageCount > 0) {
            $optimalPrice *= 1.02; // At least one image helps
        }

        $descriptionLength = strlen($ad->description ?? '');
        if ($descriptionLength > 200) {
            $optimalPrice *= 1.03; // Detailed descriptions help
        } elseif ($descriptionLength > 100) {
            $optimalPrice *= 1.01; // Decent descriptions help slightly
        }

        // Ensure reasonable bounds
        $maxPrice = ($marketPrices['max'] ?? $basePrice) * 1.5;
        $minPrice = ($marketPrices['min'] ?? $basePrice) * 0.5;

        $optimalPrice = max($minPrice, min($maxPrice, $optimalPrice));

        return $optimalPrice;
    }
    
    private function determinePricingStrategy($ad, $recommendedPrice, $marketAverage) {
        // Determine the best pricing strategy based on market position
        $priceDiffPercentage = $marketAverage > 0 ? (($recommendedPrice - $marketAverage) / $marketAverage) * 100 : 0;

        if (abs($priceDiffPercentage) <= 5) {
            return 'competitive'; // Near market average
        } elseif ($priceDiffPercentage > 10) {
            return 'premium'; // Above market average - premium positioning
        } elseif ($priceDiffPercentage < -10) {
            return 'discount'; // Below market average - competitive pricing
        } else {
            return 'balanced'; // Somewhat above/below market
        }
    }
    
    private function calculateSupplyDemandRatio($categoryId, $locationId) {
        // Calculate supply-demand ratio for the category/location
        $supply = Ad::where('category_id', $categoryId)
                   ->when($locationId, function($query) use ($locationId) {
                       return $query->where('location_id', $locationId);
                   })
                   ->where('status', 'active')
                   ->count();
                   
        // Estimate demand based on views/impressions
        $demand = Ad::where('category_id', $categoryId)
                    ->when($locationId, function($query) use ($locationId) {
                        return $query->where('location_id', $locationId);
                    })
                    ->sum('views_count') / 100; // Normalize by dividing by 100
                    
        $ratio = $supply > 0 ? $demand / $supply : 1;
        
        return [
            'supply' => $supply,
            'demand_estimate' => $demand,
            'ratio' => $ratio,
            'market_condition' => $ratio > 1 ? 'high_demand' : ($ratio < 0.5 ? 'low_demand' : 'balanced'),
        ];
    }
    
    private function getSeasonalAdjustments($categoryId) {
        // Get seasonal adjustments for pricing
        $seasonalFactors = [
            'electronics' => ['holiday_season' => 1.15, 'off_season' => 0.85],
            'fashion' => ['fashion_week' => 1.1, 'off_season' => 0.9],
            'automotive' => ['new_model_release' => 1.05, 'old_model_clearance' => 0.9],
            'general' => ['any' => 1.0],
        ];
        
        $category = strtolower(Category::find($categoryId)?->name ?? 'general');
        
        return $seasonalFactors[$category] ?? ['any' => 1.0];
    }
    
    private function getConditionAdjustments($condition) {
        // Get price adjustments based on item condition
        $adjustments = [
            'new' => 1.15,
            'excellent' => 1.05,
            'good' => 1.00,
            'fair' => 0.85,
            'poor' => 0.70,
            'any' => 1.00,
        ];
        
        return $adjustments[strtolower($condition)] ?? 1.00;
    }
    
    private function getUrgencyAdjustments($ad) {
        // Get price adjustments based on seller urgency
        $adjustment = 1.00;
        
        // If the ad has been active for a long time with few views/responses
        $daysActive = Carbon::parse($ad->created_at)->diffInDays(now());
        $viewsPerDay = $ad->views_count / max(1, $daysActive);
        $responsesPerDay = ($ad->inquires_count ?? 0) / max(1, $daysActive);
        
        if ($daysActive > 30 && $viewsPerDay < 1) {
            $adjustment *= 0.90; // Reduce price due to low interest
        } elseif ($daysActive > 60 && $viewsPerDay < 0.5) {
            $adjustment *= 0.80; // Further reduce due to very low interest
        }
        
        return $adjustment;
    }
    
    private function getPriceMovementDirection($categoryId, $locationId) {
        // Determine if prices are trending up or down for the category
        $recentAds = Ad::where('category_id', $categoryId)
                      ->when($locationId, function($query) use ($locationId) {
                          return $query->where('location_id', $locationId);
                      })
                      ->where('created_at', '>', now()->subDays(30))
                      ->get();
                      
        if ($recentAds->count() < 5) {
            return 'unknown'; // Not enough data
        }
        
        $prices = $recentAds->pluck('price')->values();
        $earlyPrices = $prices->slice(0, $prices->count()/2);
        $latePrices = $prices->slice($prices->count()/2);
        
        $earlyAvg = $earlyPrices->avg();
        $lateAvg = $latePrices->avg();
        
        return $lateAvg > $earlyAvg ? 'increasing' : ($lateAvg < $earlyAvg ? 'decreasing' : 'stable');
    }
    
    private function getSeasonalPricingTrends($categoryId) {
        // Get seasonal pricing trends
        return [
            'peak_seasons' => ['Q4' => 'high'], // Holiday season for most categories
            'off_seasons' => ['Q1' => 'low'], // Post-holiday period
            'current_season_impact' => rand(90, 110) / 100, // Current seasonal factor
        ];
    }
    
    private function getCompetitorPricingChanges($categoryId, $locationId) {
        // Get information about competitor pricing changes
        return [
            'major_price_changes' => [],
            'average_price_shift' => rand(95, 105) / 100, // 5% up/down
            'promotional_periods' => [],
        ];
    }
    
    private function generatePricingReasoning($ad, $recommendedPrice, $marketAverage) {
        // Generate human-readable reasoning for the pricing recommendation
        $priceDiff = $recommendedPrice - $marketAverage;
        $priceDiffPercent = abs(($priceDiff / $marketAverage) * 100);
        
        $reasoning = "Based on market analysis, the recommended price of " . number_format($recommendedPrice, 2) . 
                    " is " . ($priceDiff > 0 ? 'higher' : 'lower') . " than the market average of " . 
                    number_format($marketAverage, 2) . " by " . number_format($priceDiffPercent, 2) . "%.";
        
        // Add specific factors that influenced the recommendation
        if (isset($ad->condition) && strtolower($ad->condition) === 'new') {
            $reasoning .= " The item being new justifies the premium pricing.";
        } elseif (isset($ad->condition) && strtolower($ad->condition) === 'poor') {
            $reasoning .= " The poor condition warrants the discounted pricing.";
        }
        
        if ($ad->images && count($ad->images) > 3) {
            $reasoning .= " The high number of quality images increases the perceived value.";
        }
        
        if (strlen($ad->description) > 200) {
            $reasoning .= " The detailed description adds to the listing's value.";
        }
        
        return $reasoning;
    }
    
    private function analyzeSuccessFactors($ad, $categoryId, $locationId) {
        // Analyze factors that affect ad success
        $factors = [
            'title_quality' => $this->assessTitleQuality($ad->title),
            'description_quality' => $this->assessDescriptionQuality($ad->description),
            'image_quality' => $this->assessImageQuality($ad->images),
            'pricing_competitiveness' => $this->assessPricingCompetitiveness($ad),
            'category_popularity' => $this->assessCategoryPopularity($categoryId),
            'location_demand' => $this->assessLocationDemand($locationId),
            'timing_factors' => $this->assessTimingFactors($ad),
            'user_reputation' => $this->assessUserReputation($ad->user),
        ];
        
        return $factors;
    }
    
    private function assessTitleQuality($title) {
        // Assess the quality of the ad title
        $score = 50; // Base score
        
        // Length assessment
        $length = strlen($title);
        if ($length >= 10 && $length <= 100) {
            $score += 15; // Good length range
        }
        
        // Keywords assessment
        $stopWords = ['free', 'urgent', 'sale', 'wanted'];
        $keywordCount = 0;
        foreach ($stopWords as $word) {
            if (stripos($title, $word) !== false) {
                $keywordCount++;
            }
        }
        $score += min($keywordCount * 5, 15); // Up to 15 points for good keywords
        
        // Capitalization assessment
        $capsRatio = (float) preg_match_all('/[A-Z]/', $title) / max(1, $length);
        if ($capsRatio > 0.5 && $capsRatio < 0.9) {
            $score += 10; // Good but not excessive capitalization
        } elseif ($capsRatio >= 0.9) {
            $score -= 10; // Too much caps
        }
        
        return [
            'score' => min(100, max(0, $score)),
            'length' => $length,
            'keywords_present' => $keywordCount > 0,
            'capitalization' => $capsRatio,
            'assessment' => $score > 75 ? 'excellent' : ($score > 50 ? 'good' : ($score > 25 ? 'fair' : 'poor')),
        ];
    }
    
    private function assessDescriptionQuality($description) {
        // Assess the quality of the ad description
        $score = 50; // Base score
        
        // Length assessment
        $length = strlen($description);
        if ($length >= 50 && $length <= 1000) {
            $score += 25; // Good length range
        }
        
        // Keyword density
        $words = str_word_count(strtolower($description));
        $density = $words > 0 ? (substr_count($description, ' ') + 1) / $words : 0;
        
        if ($words >= 25) {
            $score += 15; // Good amount of content
        }
        
        // Formatting assessment
        $hasParagraphs = substr_count($description, "\n\n") > 0;
        $hasBulletPoints = preg_match('/[-*]\s/', $description);
        
        if ($hasParagraphs || $hasBulletPoints) {
            $score += 10; // Good formatting
        }
        
        return [
            'score' => min(100, max(0, $score)),
            'length' => $length,
            'word_count' => $words,
            'has_formatting' => $hasParagraphs || $hasBulletPoints,
            'assessment' => $score > 75 ? 'excellent' : ($score > 50 ? 'good' : ($score > 25 ? 'fair' : 'poor')),
        ];
    }
    
    private function assessImageQuality($images) {
        // Assess the quality of ad images
        $score = 50; // Base score
        
        if (empty($images)) {
            return [
                'score' => 20, // Low score for no images
                'image_count' => 0,
                'has_good_images' => false,
                'assessment' => 'poor',
            ];
        }
        
        $imageCount = is_array($images) ? count($images) : 1;
        $score += min($imageCount * 10, 40); // Up to 40 points for multiple images
        
        // Quality assessment based on image properties
        $score += min($imageCount * 5, 10); // Additional points for multiple perspectives
        
        return [
            'score' => min(100, max(0, $score)),
            'image_count' => $imageCount,
            'has_good_images' => $imageCount > 0,
            'assessment' => $score > 75 ? 'excellent' : ($score > 50 ? 'good' : ($score > 25 ? 'fair' : 'poor')),
        ];
    }
    
    private function assessPricingCompetitiveness($ad) {
        // Assess if the price is competitive
        $categoryId = $ad->category_id;
        $locationId = $ad->location_id;
        
        // Get market average for similar ads
        $marketPrices = $this->getMarketPrices($categoryId, $locationId, $ad->condition ?? 'any');
        $marketAverage = $marketPrices['average'];
        
        if (!$marketAverage) {
            return [
                'score' => 50, // Neutral score if no comparison available
                'is_competitive' => null,
                'price_ratio' => null,
            ];
        }
        
        $priceRatio = $ad->price / $marketAverage;
        $score = 50; // Base score
        
        // Adjust score based on competitiveness
        if ($priceRatio >= 0.8 && $priceRatio <= 1.2) {
            $score += 25; // Very competitive
        } elseif ($priceRatio >= 0.6 && $priceRatio <= 1.4) {
            $score += 15; // Competitive
        } elseif ($priceRatio >= 0.4 && $priceRatio <= 1.6) {
            $score += 5; // Somewhat competitive
        } else {
            $score -= 10; // Non-competitive
        }
        
        return [
            'score' => min(100, max(0, $score)),
            'is_competitive' => $priceRatio >= 0.8 && $priceRatio <= 1.2,
            'price_ratio' => $priceRatio,
            'market_average' => $marketAverage,
            'assessment' => $priceRatio > 1.5 ? 'overpriced' : ($priceRatio < 0.7 ? 'underpriced' : 'competitive'),
        ];
    }
    
    private function assessCategoryPopularity($categoryId) {
        // Assess how popular the category is
        $adCount = Ad::where('category_id', $categoryId)->count();
        
        $score = match (true) {
            $adCount > 1000 => 90,
            $adCount > 500 => 75,
            $adCount > 100 => 60,
            $adCount > 50 => 50,
            default => 30,
        };
        
        return [
            'score' => $score,
            'ad_count' => $adCount,
            'popularity_level' => $score > 75 ? 'high' : ($score > 50 ? 'medium' : 'low'),
        ];
    }
    
    private function assessLocationDemand($locationId) {
        // Assess demand in the location
        if (!$locationId) {
            return [
                'score' => 50,
                'ad_count' => 0,
                'demand_level' => 'unknown',
            ];
        }
        
        $adCount = Ad::where('location_id', $locationId)->count();
        
        $score = match (true) {
            $adCount > 2000 => 95,
            $adCount > 1000 => 80,
            $adCount > 500 => 65,
            $adCount > 100 => 50,
            default => 35,
        };
        
        return [
            'score' => $score,
            'ad_count' => $adCount,
            'demand_level' => $score > 75 ? 'high' : ($score > 50 ? 'medium' : ($score > 30 ? 'low' : 'very_low')),
        ];
    }
    
    private function assessTimingFactors($ad) {
        // Assess timing factors
        $daysActive = Carbon::parse($ad->created_at)->diffInDays(now());
        $dayOfWeek = Carbon::parse($ad->created_at)->dayOfWeek;
        
        $score = 50;
        
        // Time-based assessment
        if ($daysActive < 7) {
            $score += 15; // Fresh listings perform better initially
        } else {
            $score -= min($daysActive / 2, 20); // Performance typically decreases over time
        }
        
        // Day of week assessment (weekends might vary by category)
        if (in_array($dayOfWeek, [0, 6])) { // Weekend
            $score += 5; // Might get more visibility
        }
        
        return [
            'score' => min(100, max(0, $score)),
            'days_active' => $daysActive,
            'posted_day_of_week' => $dayOfWeek,
            'assessment' => $score > 70 ? 'good_timing' : ($score > 40 ? 'ok_timing' : 'poor_timing'),
        ];
    }
    
    private function assessUserReputation($user) {
        // Assess user reputation
        if (!$user) {
            return [
                'score' => 50,
                'verification_status' => 'unknown',
                'reputation_level' => 'neutral',
            ];
        }
        
        $score = 50;
        
        // Verification status
        if ($user->is_verified) {
            $score += 20;
        }
        
        // Activity level
        $userAds = $user->ads()->count();
        $userReviews = $user->receivedReviews()->count(); // Assume there's a relationship for reviews
        
        if ($userAds > 10) {
            $score += 10; // Experienced seller
        }
        
        if ($userReviews > 5) {
            $score += 15; // Established seller with reviews
        }
        
        return [
            'score' => min(100, max(0, $score)),
            'verification_status' => $user->is_verified ? 'verified' : 'unverified',
            'reputation_level' => $score > 75 ? 'high' : ($score > 50 ? 'medium' : 'low'),
            'activity_score' => $userAds,
            'review_score' => $userReviews,
        ];
    }
    
    private function calculateSuccessProbability($successFactors) {
        // Calculate success probability based on all factors
        $scores = array_values(array_map(function($factor) {
            return is_array($factor) && isset($factor['score']) ? $factor['score'] : 50;
        }, $successFactors));
        
        // Weighted average with some factors having more importance
        $weights = [
            'pricing_competitiveness' => 0.25,
            'title_quality' => 0.15,
            'description_quality' => 0.15,
            'image_quality' => 0.20,
            'category_popularity' => 0.10,
            'location_demand' => 0.10,
            'user_reputation' => 0.05,
        ];
        
        $weightedSum = 0;
        $totalWeight = 0;
        
        foreach ($successFactors as $key => $factor) {
            $score = is_array($factor) && isset($factor['score']) ? $factor['score'] : 50;
            $weight = $weights[$key] ?? 0.0;
            $weightedSum += $score * $weight;
            $totalWeight += $weight;
        }
        
        // If we have unused factors, give them equal weight
        $remainingWeight = 1.0 - $totalWeight;
        $unusedFactorCount = count($successFactors) - count($weights);
        $unusedWeightPerFactor = $unusedFactorCount > 0 ? $remainingWeight / $unusedFactorCount : 0;
        
        foreach ($successFactors as $key => $factor) {
            if (!isset($weights[$key])) {
                $score = is_array($factor) && isset($factor['score']) ? $factor['score'] : 50;
                $weightedSum += $score * $unusedWeightPerFactor;
            }
        }
        
        return $weightedSum;
    }
    
    private function estimateViews($successFactors) {
        // Estimate potential views based on success factors
        $baseViews = 50; // Base number of views
        $multiplier = 1.0;
        
        foreach ($successFactors as $factor) {
            if (isset($factor['score'])) {
                $multiplier *= ($factor['score'] / 50); // Adjust based on factor quality
            }
        }
        
        return (int)($baseViews * $multiplier);
    }
    
    private function estimateResponses($successFactors) {
        // Estimate potential responses based on success factors
        $estimatedViews = $this->estimateViews($successFactors);
        $responseRate = 0.05; // Base response rate
        
        // Adjust based on location and category demand
        if (isset($successFactors['location_demand']['score'])) {
            $responseRate *= ($successFactors['location_demand']['score'] / 50);
        }
        
        if (isset($successFactors['category_popularity']['score'])) {
            $responseRate *= ($successFactors['category_popularity']['score'] / 50);
        }
        
        return (int)($estimatedViews * $responseRate);
    }
    
    private function estimateConversionRate($successFactors) {
        // Estimate conversion rate based on success factors
        $baseRate = 0.10; // 10% base conversion rate
        
        $multiplier = 1.0;
        foreach ($successFactors as $factor) {
            if (isset($factor['score'])) {
                $multiplier *= ($factor['score'] / 50);
            }
        }
        
        return min(0.50, $baseRate * $multiplier); // Cap at 50%
    }
    
    private function identifyRiskFactors($ad, $successFactors) {
        // Identify risk factors that might negatively impact success
        $risks = [];
        
        // Pricing risks
        if (isset($successFactors['pricing_competitiveness'])) {
            $priceFactor = $successFactors['pricing_competitiveness'];
            if ($priceFactor['price_ratio'] > 2.0) {
                $risks[] = 'significantly_overpriced_compares_to_market';
            } elseif ($priceFactor['price_ratio'] < 0.5) {
                'quality_perception_issues_due_to_low_price';
            }
        }
        
        // Title risks
        if (isset($successFactors['title_quality'])) {
            $titleFactor = $successFactors['title_quality'];
            if ($titleFactor['score'] < 30) {
                $risks[] = 'poor_title_quality_affecting_visibility';
            }
        }
        
        // Description risks
        if (isset($successFactors['description_quality'])) {
            $descFactor = $successFactors['description_quality'];
            if ($descFactor['score'] < 30) {
                $risks[] = 'insufficient_description_affecting_conversion';
            }
        }
        
        // Image risks
        if (isset($successFactors['image_quality'])) {
            $imgFactor = $successFactors['image_quality'];
            if ($imgFactor['score'] < 30) {
                $risks[] = 'poor_image_quality_affecting_trust';
            }
        }
        
        return $risks;
    }
    
    private function getComparativeAnalysis($ad, $categoryId, $locationId) {
        // Get comparative analysis with similar successful ads
        $similarAds = Ad::where('category_id', $categoryId)
                       ->when($locationId, function($query) use ($locationId) {
                           return $query->where('location_id', $locationId);
                       })
                       ->where('id', '!=', $ad->id)
                       ->where('status', 'active')
                       ->where('price', '>', 0)
                       ->orderBy('views_count', 'desc')
                       ->limit(5)
                       ->get();
        
        $comparisons = [];
        foreach ($similarAds as $similarAd) {
            $comparisons[] = [
                'id' => $similarAd->id,
                'title' => $similarAd->title,
                'price' => $similarAd->price,
                'views' => $similarAd->views_count ?? 0,
                'responses' => $similarAd->inquiries_count ?? 0,
                'created_at' => $similarAd->created_at->format('Y-m-d'),
                'success_metrics' => [
                    'view_to_response_ratio' => $similarAd->views_count > 0 ? ($similarAd->inquiries_count ?? 0) / $similarAd->views_count : 0,
                ],
            ];
        }
        
        return $comparisons;
    }
    
    private function generateImprovementSuggestions($ad, $successFactors) {
        // Generate improvement suggestions based on weaknesses
        $suggestions = [];
        
        // Title suggestions
        if (isset($successFactors['title_quality'])) {
            $titleFactor = $successFactors['title_quality'];
            if ($titleFactor['score'] < 60) {
                $suggestions[] = 'Improve your ad title by using relevant keywords and keeping it between 10-100 characters';
            }
        }
        
        // Description suggestions
        if (isset($successFactors['description_quality'])) {
            $descFactor = $successFactors['description_quality'];
            if ($descFactor['score'] < 60) {
                $suggestions[] = 'Write a more detailed description with at least 50 words, good formatting, and key features';
            }
        }
        
        // Image suggestions
        if (isset($successFactors['image_quality'])) {
            $imgFactor = $successFactors['image_quality'];
            if ($imgFactor['score'] < 60) {
                $suggestions[] = 'Add more high-quality images (at least 3-5) showing different angles and features';
            }
        }
        
        // Pricing suggestions
        if (isset($successFactors['pricing_competitiveness'])) {
            $priceFactor = $successFactors['pricing_competitiveness'];
            if ($priceFactor['price_ratio'] > 1.5) {
                $suggestions[] = 'Consider lowering your price to be more competitive with similar listings';
            } elseif ($priceFactor['price_ratio'] < 0.7) {
                $suggestions[] = 'Consider increasing your price slightly to avoid perception of low quality';
            }
        }
        
        // General suggestions
        $suggestions[] = 'Post your ad at optimal times when your target audience is most active';
        $suggestions[] = 'Respond promptly to inquiries to improve your seller reputation';
        $suggestions[] = 'Consider featuring your ad during high-traffic periods';
        
        return $suggestions;
    }
    
    private function calculateEngagementScore($title, $description, $images) {
        // Calculate engagement score based on content quality
        $score = 50; // Base score
        
        // Title engagement
        $titleLength = strlen($title);
        if ($titleLength >= 10 && $titleLength <= 100) {
            $score += 10;
        }
        
        // Description engagement
        $descLength = strlen($description);
        if ($descLength >= 50 && $descLength <= 1000) {
            $score += 15;
        }
        
        // Image engagement
        $imageCount = is_array($images) ? count($images) : 1;
        $score += min($imageCount * 5, 20); // Up to 20 points for images
        
        // Content richness
        if (preg_match('/[A-Z][a-z].*[A-Z]/', $title)) {
            $score += 5; // Shows mixed case usage
        }
        
        if (strpos($description, "\n") !== false) {
            $score += 5; // Has some formatting
        }
        
        return min(100, max(0, $score));
    }
    
    private function calculateConversionProbability($price, $marketPosition) {
        // Calculate conversion probability based on price and market position
        $baseProbability = 0.15; // Base 15% conversion probability
        
        // Adjust based on pricing competitiveness
        $competitiveFactor = $marketPosition['is_competitive'] ?? false ? 1.2 : 0.8;
        
        // Adjust based on price (lower prices tend to convert better)
        $priceFactor = $price > 100000 ? 0.8 : ($price > 50000 ? 0.9 : 1.0);
        
        $probability = $baseProbability * $competitiveFactor * $priceFactor;
        
        return min(0.75, $probability); // Cap at 75%
    }
    
    private function generateOptimizationTips($ad, $successFactors) {
        // Generate optimization tips based on success factors
        $tips = [
            'immediate_actions' => [],
            'short_term_improvements' => [],
            'long_term_strategies' => [],
        ];
        
        if (isset($successFactors['title_quality']) && $successFactors['title_quality']['score'] < 60) {
            $tips['immediate_actions'][] = 'Improve your ad title with better keywords';
        }
        
        if (isset($successFactors['description_quality']) && $successFactors['description_quality']['score'] < 60) {
            $tips['immediate_actions'][] = 'Add more detailed description with key features';
        }
        
        if (isset($successFactors['image_quality']) && $successFactors['image_quality']['score'] < 60) {
            $tips['immediate_actions'][] = 'Upload higher quality images';
        }
        
        if (isset($successFactors['pricing_competitiveness'])) {
            $priceFactor = $successFactors['pricing_competitiveness'];
            if ($priceFactor['price_ratio'] > 1.5) {
                $tips['short_term_improvements'][] = 'Consider a temporary discount to boost sales';
            } else if ($priceFactor['price_ratio'] < 0.7) {
                $tips['short_term_improvements'][] = 'Add more value propositions to justify pricing';
            }
        }
        
        return $tips;
    }
    
    private function findSimilarAds($ad) {
        // Find similar ads based on category, location, price, and content
        $query = Ad::where('category_id', $ad->category_id)
                  ->where('id', '!=', $ad->id)
                  ->where('status', 'active');
                  
        if ($ad->location_id) {
            $query->where('location_id', $ad->location_id);
        }
        
        // Filter by approximate price (within 30%)
        $priceRangeMin = $ad->price * 0.7;
        $priceRangeMax = $ad->price * 1.3;
        $query->whereBetween('price', [$priceRangeMin, $priceRangeMax]);
        
        return $query->limit(10)->get();
    }
    
    private function calculateSimilarityScore($ad1, $ad2) {
        // Calculate similarity score based on multiple attributes
        $score = 0;
        $maxScore = 100;
        
        // Title similarity (30 points)
        $titleSim = $this->calculateTextSimilarity($ad1->title, $ad2->title);
        $score += $titleSim * 0.3 * 30;
        
        // Category match (20 points)
        if ($ad1->category_id === $ad2->category_id) {
            $score += 20;
        }
        
        // Location match (15 points)
        if ($ad1->location_id && $ad2->location_id && $ad1->location_id === $ad2->location_id) {
            $score += 15;
        }
        
        // Price similarity (20 points)
        $priceDiff = abs($ad1->price - $ad2->price);
        $priceThreshold = min($ad1->price, $ad2->price) * 0.1; // 10% threshold
        if ($priceDiff <= $priceThreshold) {
            $score += 20;
        } else {
            $score += max(0, 20 - ($priceDiff / $priceThreshold * 10));
        }
        
        // Description similarity (15 points)
        $descSim = $this->calculateTextSimilarity($ad1->description, $ad2->description);
        $score += $descSim * 0.15 * 15;
        
        return min(100, max(0, $score));
    }
    
    private function calculateTextSimilarity($text1, $text2) {
        // Calculate text similarity using a simple word overlap method
        if (empty($text1) || empty($text2)) {
            return 0;
        }
        
        $words1 = array_flip(array_map('trim', preg_split('/[\s,\.\!\?]+/', strtolower($text1))));
        $words2 = array_flip(array_map('trim', preg_split('/[\s,\.\!\?]+/', strtolower($text2))));
        
        $common = array_intersect_key($words1, $words2);
        $total = array_merge($words1, $words2);
        
        return count($common) / max(1, count($total));
    }
    
    private function compareAdAttributes($ad1, $ad2) {
        // Compare specific attributes between two ads
        $matches = [];
        
        if ($ad1->title === $ad2->title) {
            $matches[] = 'title';
        }
        
        if ($ad1->category_id === $ad2->category_id) {
            $matches[] = 'category';
        }
        
        if ($ad1->location_id === $ad2->location_id) {
            $matches[] = 'location';
        }
        
        if (abs($ad1->price - $ad2->price) < min($ad1->price, $ad2->price) * 0.05) { // 5% difference
            $matches[] = 'price';
        }
        
        if ($ad1->condition === $ad2->condition) {
            $matches[] = 'condition';
        }
        
        return $matches;
    }
    
    private function analyzeImageSimilarity($ad1, $ad2) {
        // Analyze image similarity (in a production system, this would use computer vision)
        // For now, we'll simulate the result
        $hasSimilarImages = false;
        $confidence = 0;
        
        // Check if both ads have images
        if ($ad1->images && $ad2->images) {
            $images1 = is_array($ad1->images) ? $ad1->images : [$ad1->images];
            $images2 = is_array($ad2->images) ? $ad2->images : [$ad2->images];
            
            // Check if they have similar number of images or same filenames
            $hasSimilarImages = count($images1) === count($images2);
            $confidence = $hasSimilarImages ? 75 : 25;
        }
        
        return [
            'has_similar_images' => $hasSimilarImages,
            'confidence' => $confidence,
            'details' => [
                'ad1_image_count' => $ad1->images ? (is_array($ad1->images) ? count($ad1->images) : 1) : 0,
                'ad2_image_count' => $ad2->images ? (is_array($ad2->images) ? count($ad2->images) : 1) : 0,
            ],
        ];
    }
    
    private function analyzeTextSimilarity($ad1, $ad2) {
        // Analyze text similarity between ad titles and descriptions
        $titleSimilarity = $this->calculateTextSimilarity($ad1->title, $ad2->title);
        $descSimilarity = $this->calculateTextSimilarity($ad1->description, $ad2->description);
        
        $overallSimilarity = ($titleSimilarity + $descSimilarity) / 2;
        $confidence = min(100, max(0, $overallSimilarity * 100));
        
        return [
            'similarity' => $overallSimilarity,
            'confidence' => $confidence,
            'breakdown' => [
                'title_similarity' => $titleSimilarity,
                'description_similarity' => $descSimilarity,
            ],
        ];
    }
    
    private function calculatePriceSimilarity($ad1, $ad2) {
        // Calculate price similarity percentage
        if ($ad1->price === 0 || $ad2->price === 0) {
            return 0;
        }
        
        $difference = abs($ad1->price - $ad2->price);
        $average = ($ad1->price + $ad2->price) / 2;
        
        return $average > 0 ? (1 - ($difference / $average)) * 100 : 0;
    }
    
    private function calculateLocationSimilarity($ad1, $ad2) {
        // Calculate location similarity
        if (!$ad1->location_id || !$ad2->location_id) {
            return $ad1->location_id === $ad2->location_id ? 100 : 0;
        }
        
        return $ad1->location_id === $ad2->location_id ? 100 : 0;
    }
    
    private function generateDuplicateReasoning($ad1, $ad2, $similarityScore) {
        // Generate reasoning for why these might be duplicates
        $reasons = [];
        
        if ($ad1->title === $ad2->title) {
            $reasons[] = "Both ads have identical titles";
        } else {
            $titleSim = $this->calculateTextSimilarity($ad1->title, $ad2->title);
            if ($titleSim > 0.8) {
                $reasons[] = "Both ads have very similar titles";
            }
        }
        
        $priceDiff = abs($ad1->price - $ad2->price);
        $priceThreshold = min($ad1->price, $ad2->price) * 0.05; // 5% threshold
        if ($priceDiff <= $priceThreshold) {
            $reasons[] = "Both ads have nearly identical pricing";
        }
        
        $descSim = $this->calculateTextSimilarity($ad1->description, $ad2->description);
        if ($descSim > 0.8) {
            $reasons[] = "Both ads have very similar descriptions";
        }
        
        if ($ad1->category_id === $ad2->category_id) {
            $reasons[] = "Both ads are in the same category";
        }
        
        if ($ad1->location_id && $ad2->location_id && $ad1->location_id === $ad2->location_id) {
            $reasons[] = "Both ads are in the same location";
        }
        
        if ($similarityScore > 90) {
            $reasons[] = "Overall similarity between ads is extremely high";
        } elseif ($similarityScore > 80) {
            $reasons[] = "Overall similarity between ads is very high";
        }
        
        return implode("; ", $reasons);
    }
    
    private function analyzeUserBehavior($user) {
        // Analyze user behavior patterns for fraud detection
        $flags = [
            'risk_score' => 0,
            'indicators' => [],
            'behavioral_patterns' => [],
            'suspicious_activities' => [],
        ];
        
        if (!$user) {
            return $flags;
        }
        
        $recentAds = $user->ads()->where('created_at', '>', now()->subHours(24))->get();
        
        // Check if user is creating many ads in a short time
        if ($recentAds->count() > 5) {
            $flags['risk_score'] += 20;
            $flags['indicators'][] = 'multiple_ads_created_in_short_period';
            $flags['suspicious_activities'][] = 'rapid_ad_creation_pattern';
            $flags['behavioral_patterns'][] = 'bulk_listing_behavior';
        }
        
        // Check if user has incomplete profile
        if (empty($user->phone) || empty($user->address)) {
            $flags['risk_score'] += 10;
            $flags['indicators'][] = 'incomplete_profile';
        }
        
        // Check if user has verification
        if (!$user->is_verified) {
            $flags['risk_score'] += 5;
            $flags['indicators'][] = 'unverified_account';
        }
        
        // Check for similar ads across multiple accounts
        $recentAdTitles = $recentAds->pluck('title')->filter()->toArray();
        $similarInOtherAccounts = 0;
        
        // This would check for ads with similar titles in other accounts
        foreach ($recentAdTitles as $title) {
            $similarCount = Ad::where('title', $title)
                             ->where('user_id', '!=', $user->id)
                             ->count();
            if ($similarCount > 0) {
                $similarInOtherAccounts++;
            }
        }
        
        if ($similarInOtherAccounts > 0) {
            $flags['risk_score'] += 15;
            $flags['indicators'][] = 'similar_ads_across_accounts';
        }
        
        return $flags;
    }
    
    private function analyzeAdContent($ad) {
        // Analyze ad content for potential fraud indicators
        $flags = [
            'risk_score' => 0,
            'indicators' => [],
            'suspicious_activities' => [],
        ];
        
        if (!$ad) {
            return $flags;
        }
        
        // Check for suspicious keywords in title or description
        $suspiciousKeywords = ['free', 'urgent', 'act_now', 'limited_time', 'cash_only', 'wire_transfer'];
        $content = strtolower($ad->title . ' ' . $ad->description);
        
        foreach ($suspiciousKeywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                $flags['risk_score'] += 10;
                $flags['indicators'][] = 'suspicious_keyword_' . $keyword;
                $flags['suspicious_activities'][] = 'use_of_suspicious_keywords';
            }
        }
        
        // Check if price seems too good to be true
        $categoryAvg = Ad::where('category_id', $ad->category_id)
                        ->avg('price') ?? 0;
        
        if ($categoryAvg > 0 && $ad->price < ($categoryAvg * 0.1)) { // Price is less than 10% of category avg
            $flags['risk_score'] += 25;
            $flags['indicators'][] = 'suspiciously_low_price';
            $flags['suspicious_activities'][] = 'bait_advertising';
        }
        
        // Check if ad has no images
        if (empty($ad->images)) {
            $flags['risk_score'] += 5;
            $flags['indicators'][] = 'no_images_provided';
        }
        
        // Check if description is too short
        if (strlen($ad->description) < 20) {
            $flags['risk_score'] += 5;
            $flags['indicators'][] = 'sparse_description';
        }
        
        return $flags;
    }
    
    private function analyzePayment($payment) {
        // Analyze payment for fraud indicators
        $flags = [
            'risk_score' => 0,
            'indicators' => [],
            'suspicious_activities' => [],
        ];
        
        if (!$payment) {
            return $flags;
        }
        
        // Check for unusually high amounts
        if ($payment->amount > 1000000) { // Over 1M naira
            $flags['risk_score'] += 20;
            $flags['indicators'][] = 'unusually_high_amount';
            $flags['suspicious_activities'][] = 'potential_money_laundering';
        }
        
        // Check if payment was failed previously
        if (isset($payment->status) && $payment->status === 'failed') {
            $flags['risk_score'] += 10;
            $flags['indicators'][] = 'previous_failed_payments';
        }
        
        return $flags;
    }
    
    private function checkCommonFraudPatterns($ad, $user, $payment) {
        // Check for common fraud patterns across the system
        $flags = [
            'risk_score' => 0,
            'indicators' => [],
            'suspicious_activities' => [],
        ];
        
        if ($user && $ad) {
            // Check for bulk posting patterns from same IP (would need to store user IP)
            // This is a simplified version - in reality, you'd track user IPs
            
            // Check for repeated phone numbers across accounts
            if ($user->phone) {
                $otherUsersWithSamePhone = User::where('phone', $user->phone)
                                               ->where('id', '!=', $user->id)
                                               ->count();
                
                if ($otherUsersWithSamePhone > 0) {
                    $flags['risk_score'] += 15;
                    $flags['indicators'][] = 'multiple_accounts_same_phone';
                    $flags['suspicious_activities'][] = 'multiple_accounts_same_contact';
                }
            }
            
            // Check for same email domain pattern abuse
            $emailDomain = substr(strrchr($user->email, "@"), 1);
            if (in_array($emailDomain, ['tempmail.com', 'guerrillamail.com', '10minutemail.com'])) {
                $flags['risk_score'] += 25;
                $flags['indicators'][] = 'temporary_email_service';
                $flags['suspicious_activities'][] = 'use_of_temporary_email';
            }
        }
        
        return $flags;
    }
    
    private function determineFraudType($indicators) {
        // Determine the type of fraud based on indicators
        $type = 'general';
        
        if (in_array('multiple_ads_created_in_short_period', $indicators)) {
            $type = 'bulk_spam';
        } elseif (in_array('suspiciously_low_price', $indicators)) {
            $type = 'bait_advertising';
        } elseif (in_array('temporary_email_service', $indicators)) {
            $type = 'account_fraud';
        } elseif (in_array('suspicious_keyword_free', $indicators) || in_array('suspicious_keyword_cash_only', $indicators)) {
            $type = 'scam';
        } elseif (in_array('unusually_high_amount', $indicators)) {
            $type = 'money_laundering';
        }
        
        return $type;
    }
    
    private function generateFraudAnalysis($indicators, $behavioralPatterns, $suspiciousActivities) {
        // Generate analysis details for fraud detection
        return [
            'summary' => 'Multiple indicators suggest potential fraudulent activity',
            'primary_indicators' => $indicators,
            'behavioral_analysis' => $behavioralPatterns,
            'suspicious_activities' => $suspiciousActivities,
            'recommendation' => count($indicators) > 3 ? 'manual_review_required' : 'monitor_closely',
        ];
    }
    
    private function calculateUserBehaviorConfidence($user) {
        // Calculate confidence in user behavior assessment
        if (!$user) {
            return 0;
        }
        
        $factors = [
            'account_age' => $user->created_at->diffInDays(now()) > 30 ? 100 : 50,
            'ads_posted' => $user->ads()->count() > 10 ? 100 : ($user->ads()->count() > 5 ? 75 : 50),
            'verification_status' => $user->is_verified ? 100 : 50,
            'profile_completeness' => (empty($user->phone) ? 0 : 50) + (empty($user->address) ? 0 : 50),
        ];
        
        return array_sum($factors) / count($factors);
    }
    
    private function calculateContentAnalysisConfidence($ad) {
        // Calculate confidence in content analysis
        if (!$ad) {
            return 0;
        }
        
        $factors = [
            'title_length' => strlen($ad->title) > 10 ? 100 : 50,
            'description_length' => strlen($ad->description) > 50 ? 100 : 50,
            'has_images' => !empty($ad->images) ? 100 : 30,
        ];
        
        return array_sum($factors) / count($factors);
    }
    
    private function calculatePaymentIndicatorConfidence($payment) {
        // Calculate confidence in payment indicators
        if (!$payment) {
            return 0;
        }
        
        $factors = [
            'payment_method' => 100, // All payment methods can be analyzed
            'amount_validity' => $payment->amount > 0 ? 100 : 0,
            'status' => isset($payment->status) ? 100 : 50,
        ];
        
        return array_sum($factors) / count($factors);
    }
    
    private function calculatePatternMatchingConfidence($patternFlags) {
        // Calculate confidence in pattern matching
        $count = count($patternFlags['indicators']);
        return min(100, $count * 20);
    }
    
    private function generateRecommendedActions($riskScore, $type) {
        // Generate recommended actions based on risk score and type
        $actions = [];
        
        if ($riskScore > 80) {
            $actions[] = 'immediate_account_suspension';
            $actions[] = 'refund_pending_transactions';
            $actions[] = 'escalate_to_security_team';
        } elseif ($riskScore > 60) {
            $actions[] = 'manual_review_required';
            $actions[] = 'restrict_ad_posting';
            $actions[] = 'require_additional_verification';
        } elseif ($riskScore > 40) {
            $actions[] = 'monitor_closely';
            $actions[] = 'verify_user_identity';
            $actions[] = 'warn_user_of_policy_violations';
        } else {
            $actions[] = 'continue_normal_operations';
            $actions[] = 'monitor_for_changes';
        }
        
        return $actions;
    }
}