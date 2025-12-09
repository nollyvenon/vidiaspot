<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use App\Services\MySqlToSqliteCacheService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for voice search with natural language processing
 */
class VoiceSearchService
{
    protected $cacheService;
    
    public function __construct(MySqlToSqliteCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Process voice input and convert to text query
     */
    public function processVoiceSearch($audioFile, string $language = 'en'): string
    {
        $fileHash = md5_file($audioFile->getPathname());
        $cacheKey = "voice_search_{$fileHash}_{$language}";
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($audioFile, $language) {
                return $this->convertSpeechToText($audioFile, $language);
            },
            3600
        );
    }
    
    /**
     * Convert speech to text using audio processing
     */
    private function convertSpeechToText($audioFile, string $language): string
    {
        // In a real implementation, this would call a speech-to-text API
        // like Google Speech-to-Text, AWS Transcribe, or OpenAI Whisper API
        
        // For now, return a simulated conversion
        if (function_exists('config') && config('services.openai.api_key')) {
            // Would call OpenAI Whisper API here
            // $response = Http::withHeaders(['Authorization' => 'Bearer ' . config('services.openai.api_key')])
            // ->attach('file', fopen($audioFile->getPathname(), 'r'), $audioFile->getClientOriginalName())
            // ->post('https://api.openai.com/v1/audio/transcriptions', [
            //     'model' => 'whisper-1',
            //     'language' => $language
            // ]);
            // return $response->json('text');
        }
        
        // Simulated response
        return "iPhone for sale in Lagos Nigeria";
    }
    
    /**
     * Process natural language query to structured search parameters
     */
    public function processNaturalLanguageQuery(string $queryText): array
    {
        $cacheKey = "nl_query_" . md5($queryText);
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($queryText) {
                return $this->parseNaturalLanguage($queryText);
            },
            3600
        );
    }
    
    /**
     * Parse natural language to extract search parameters
     */
    private function parseNaturalLanguage(string $queryText): array
    {
        // Use basic NLP to extract entities and intents
        $queryText = strtolower($queryText);
        
        // Extract location (simple pattern matching - would use NLP in production)
        $location = null;
        preg_match('/in\s+([a-zA-Z\s]+)/', $queryText, $locationMatches);
        if (!empty($locationMatches[1])) {
            $location = trim($locationMatches[1]);
        }
        
        // Extract price range
        $minPrice = null;
        $maxPrice = null;
        preg_match('/between\s+(\d+)\s+and\s+(\d+)/', $queryText, $priceMatches);
        if (!empty($priceMatches)) {
            $minPrice = (int)$priceMatches[1];
            $maxPrice = (int)$priceMatches[2];
        } else {
            preg_match('/under\s+(\d+)/', $queryText, $maxPriceMatch);
            if (!empty($maxPriceMatch[1])) {
                $maxPrice = (int)$maxPriceMatch[1];
            }
            preg_match('/over\s+(\d+)/', $queryText, $minPriceMatch);
            if (!empty($minPriceMatch[1])) {
                $minPrice = (int)$minPriceMatch[1];
            }
        }
        
        // Extract category/keywords
        $keywords = [];
        $stopWords = ['for', 'sale', 'in', 'the', 'a', 'an', 'and', 'or', 'but', 'with', 'by'];
        $words = explode(' ', $queryText);
        
        foreach ($words as $word) {
            $cleanWord = trim($word, " \t\n\r\0\x0B.,!?;:");
            if (!empty($cleanWord) && !in_array(strtolower($cleanWord), $stopWords)) {
                $keywords[] = $cleanWord;
            }
        }
        
        $primaryKeyword = !empty($keywords) ? $keywords[0] : '';
        
        return [
            'query' => $queryText,
            'primary_keyword' => $primaryKeyword,
            'keywords' => $keywords,
            'location' => $location,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'raw_intent' => 'search',
            'category_guess' => $this->inferCategory($primaryKeyword)
        ];
    }
    
    /**
     * Infer category from keyword
     */
    private function inferCategory(string $keyword): ?string
    {
        $keyword = strtolower($keyword);
        
        $categories = [
            'phone' => 'Mobile Phones',
            'car' => 'Cars',
            'laptop' => 'Laptops',
            'tv' => 'TVs & Home Theater',
            'sofa' => 'Furniture',
            'bed' => 'Furniture',
            'kitchen' => 'Home & Kitchen',
            'book' => 'Books',
            'fashion' => 'Clothing & Fashion',
            'shoe' => 'Footwear',
            'property' => 'Property',
            'real estate' => 'Property',
            'bike' => 'Bicycles',
            'motorcycle' => 'Motorcycles',
        ];
        
        foreach ($categories as $key => $category) {
            if (strpos($keyword, $key) !== false) {
                return $category;
            }
        }
        
        return null;
    }
}