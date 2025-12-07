<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIRecommendationService
{
    protected $openaiApiKey;
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
        $this->openaiApiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
    }

    /**
     * Get AI-powered recommendations using OpenAI or similar service.
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAIRecommendations($userId, $limit = 10)
    {
        // First, try to get recommendations using our basic algorithm
        $basicRecommendations = $this->recommendationService->getRecommendedAds($userId, $limit);

        // If AI API key is configured, enhance recommendations with AI
        if ($this->openaiApiKey) {
            try {
                return $this->getAIEnhancedRecommendations($userId, $basicRecommendations, $limit);
            } catch (\Exception $e) {
                Log::warning('AI Recommendation failed: ' . $e->getMessage());
                // Fall back to basic recommendations
                return $basicRecommendations;
            }
        }

        return $basicRecommendations;
    }

    /**
     * Get recommendations enhanced with AI analysis.
     *
     * @param int $userId
     * @param \Illuminate\Database\Eloquent\Collection $basicRecommendations
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAIEnhancedRecommendations($userId, $basicRecommendations, $limit)
    {
        $user = User::find($userId);
        if (!$user) {
            return $basicRecommendations;
        }

        // Get more user data for better AI analysis
        $userAdIds = Ad::where('user_id', $userId)
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->pluck('id')
            ->toArray();

        $userAds = [];
        if (!empty($userAdIds)) {
            $userAds = Ad::with(['category', 'currency'])
                ->whereIn('id', $userAdIds)
                ->get()
                ->map(function ($ad) {
                    return [
                        'title' => $ad->title,
                        'description' => $ad->description,
                        'category' => $ad->category->name ?? null,
                        'price' => $ad->price,
                        'currency' => $ad->currency_code,
                        'condition' => $ad->condition,
                        'location' => $ad->location
                    ];
                })
                ->toArray();
        }

        // Prepare the prompt for OpenAI
        $prompt = $this->buildRecommendationPrompt($user, $userAds, $limit);

        // Call OpenAI API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 200,
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiResponse = $data['choices'][0]['message']['content'] ?? '';

            // Parse the AI response to extract ad IDs
            $recommendedAdIds = $this->parseAIResponse($aiResponse);

            if (!empty($recommendedAdIds)) {
                // Get the actual ads
                $aiRecommendations = Ad::with(['user', 'category', 'images', 'currency'])
                    ->whereIn('id', $recommendedAdIds)
                    ->where('status', 'active')
                    ->limit($limit)
                    ->get();

                // Combine with basic recommendations if needed
                return $aiRecommendations->concat(
                    $basicRecommendations->whereNotIn('id', $recommendedAdIds->pluck('id'))
                )->unique('id')->take($limit);
            }
        }

        return $basicRecommendations;
    }

    /**
     * Build a prompt for the AI service.
     *
     * @param User $user
     * @param array $userAds
     * @param int $limit
     * @return string
     */
    private function buildRecommendationPrompt($user, $userAds, $limit)
    {
        $userData = [
            'location' => $user->city . ', ' . $user->state . ', ' . $user->country,
            'recent_ads' => $userAds,
        ];

        return "Based on this user's profile: " . json_encode($userData) . 
               ", recommend {$limit} ads that might interest them from a marketplace. " .
               "Respond with only the ad IDs as a JSON array like [1, 5, 12, 23]. " .
               "Do not include any other text or explanation. Only return the JSON array.";
    }

    /**
     * Parse the AI response to extract ad IDs.
     *
     * @param string $response
     * @return array
     */
    private function parseAIResponse($response)
    {
        // Remove any non-JSON text
        $jsonStart = strpos($response, '[');
        $jsonEnd = strpos($response, ']');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($response, $jsonStart, $jsonEnd - $jsonStart + 1);
            $ids = json_decode($jsonString, true);
            
            if (is_array($ids)) {
                return collect($ids)->filter(function ($id) {
                    return is_numeric($id);
                })->values();
            }
        }

        return collect();
    }

    /**
     * Get AI-powered similar ads.
     *
     * @param int $adId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAISimilarAds($adId, $limit = 10)
    {
        $targetAd = Ad::with(['category', 'currency'])->find($adId);
        
        if (!$targetAd) {
            return $this->recommendationService->getSimilarAds($adId, $limit);
        }

        if ($this->openaiApiKey) {
            try {
                return $this->getAISimilarAdsRecommendations($targetAd, $limit);
            } catch (\Exception $e) {
                Log::warning('AI Similar Ads failed: ' . $e->getMessage());
                return $this->recommendationService->getSimilarAds($adId, $limit);
            }
        }

        return $this->recommendationService->getSimilarAds($adId, $limit);
    }

    /**
     * Get AI-powered similar ads recommendations.
     *
     * @param Ad $targetAd
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getAISimilarAdsRecommendations($targetAd, $limit)
    {
        $prompt = "Find {$limit} ads similar to this one: " .
                 "Title: {$targetAd->title}, " .
                 "Description: {$targetAd->description}, " .
                 "Category: {$targetAd->category->name ?? 'N/A'}, " .
                 "Price: {$targetAd->price} {$targetAd->currency_code}, " .
                 "Condition: {$targetAd->condition}, " .
                 "Location: {$targetAd->location}. " .
                 "Respond with only the ad IDs as a JSON array like [1, 5, 12, 23]. " .
                 "Do not include any other text or explanation. Only return the JSON array.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 200,
            'temperature' => 0.7,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $aiResponse = $data['choices'][0]['message']['content'] ?? '';

            $recommendedAdIds = $this->parseAIResponse($aiResponse);

            if (!empty($recommendedAdIds)) {
                return Ad::with(['user', 'category', 'images', 'currency'])
                    ->whereIn('id', $recommendedAdIds)
                    ->where('status', 'active')
                    ->where('id', '!=', $targetAd->id)
                    ->limit($limit)
                    ->get();
            }
        }

        // Fallback to basic algorithm
        return Ad::with(['user', 'category', 'images', 'currency'])
            ->where('status', 'active')
            ->where('category_id', $targetAd->category_id)
            ->where('id', '!=', $targetAd->id)
            ->orderByRaw('ABS(price - ?)', [$targetAd->price])
            ->limit($limit)
            ->get();
    }
}