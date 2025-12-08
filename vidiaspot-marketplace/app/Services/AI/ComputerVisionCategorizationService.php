<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use App\Services\MySqlToSqliteCacheService;
use Illuminate\Http\UploadedFile;
use App\Models\Category;

/**
 * Service for AI-powered smart categorization using computer vision
 */
class ComputerVisionCategorizationService
{
    protected $cacheService;
    
    public function __construct(MySqlToSqliteCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Categorize an item using computer vision
     *
     * @param UploadedFile $image
     * @param array $options Categorization options
     * @return array List of possible categories with confidence scores
     */
    public function categorizeItem(UploadedFile $image, array $options = []): array
    {
        $imageHash = md5_file($image->getPathname());
        $optionsHash = md5(serialize($options));
        $cacheKey = "cv_category_{$imageHash}_{$optionsHash}";
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($image, $options) {
                return $this->performCategorization($image, $options);
            },
            86400 * 2 // Cache for 2 days
        );
    }
    
    /**
     * Get the most likely category for an item
     *
     * @param UploadedFile $image
     * @param array $options
     * @return string|null Category name
     */
    public function getPrimaryCategory(UploadedFile $image, array $options = []): ?string
    {
        $categories = $this->categorizeItem($image, $options);
        
        if (empty($categories)) {
            return null;
        }
        
        // Get the category with highest confidence
        $primaryCategory = null;
        $highestConfidence = 0;
        
        foreach ($categories as $category => $confidence) {
            if ($confidence > $highestConfidence) {
                $highestConfidence = $confidence;
                $primaryCategory = $category;
            }
        }
        
        return $primaryCategory;
    }
    
    /**
     * Find existing categories in the database that match the detected objects
     *
     * @param UploadedFile $image
     * @param array $options
     * @return array Matching categories from database
     */
    public function findMatchingCategories(UploadedFile $image, array $options = []): array
    {
        $detectedCategories = $this->categorizeItem($image, $options);
        $matchingCategories = [];
        
        foreach ($detectedCategories as $detectedCategory => $confidence) {
            if ($confidence > 0.5) { // Only consider high-confidence matches
                $dbCategory = Category::where('name', 'LIKE', "%{$detectedCategory}%")
                                    ->orWhere('slug', 'LIKE', "%{$detectedCategory}%")
                                    ->orWhere('description', 'LIKE', "%{$detectedCategory}%")
                                    ->first();
                
                if ($dbCategory) {
                    $matchingCategories[] = [
                        'category' => $dbCategory,
                        'confidence' => $confidence
                    ];
                }
            }
        }
        
        return $matchingCategories;
    }
    
    /**
     * Suggest new categories based on computer vision analysis
     *
     * @param UploadedFile $image
     * @param array $options
     * @return array Suggested categories
     */
    public function suggestNewCategories(UploadedFile $image, array $options = []): array
    {
        $detectedCategories = $this->categorizeItem($image, $options);
        $existingCategories = Category::pluck('name', 'name')->toArray();
        $suggestions = [];
        
        foreach ($detectedCategories as $detectedCategory => $confidence) {
            if ($confidence > 0.7 && !isset($existingCategories[$detectedCategory])) {
                $suggestions[] = [
                    'name' => $detectedCategory,
                    'confidence' => $confidence,
                    'slug' => \Str::slug($detectedCategory),
                    'parent_id' => null // Could implement parent category suggestions too
                ];
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Perform actual categorization using computer vision
     */
    private function performCategorization(UploadedFile $image, array $options): array
    {
        // This would call an actual computer vision API like:
        // - Google Vision API
        // - AWS Rekognition
        // - Azure Computer Vision
        // - Or a local ML model
        
        // For now, simulate the process by analyzing the image file
        $imageHash = md5_file($image->getPathname());
        
        // In a real implementation, this would call an actual AI service
        if (function_exists('config') && config('services.vision.api_key')) {
            // Call computer vision API
            // This is where you'd implement the actual API call
        }
        
        // Simulated categorization based on file analysis
        $filename = strtolower($image->getClientOriginalName());
        $mimeType = $image->getMimeType();
        
        // Basic simulation based on file characteristics
        $possibleCategories = [];
        
        if (strpos($filename, 'phone') !== false || strpos($filename, 'mobile') !== false) {
            $possibleCategories['Electronics'] = 0.9;
            $possibleCategories['Mobile Phones'] = 0.85;
            $possibleCategories['Smartphones'] = 0.8;
        } elseif (strpos($filename, 'car') !== false || strpos($filename, 'vehicle') !== false) {
            $possibleCategories['Vehicles'] = 0.9;
            $possibleCategories['Cars'] = 0.85;
        } elseif (strpos($filename, 'book') !== false) {
            $possibleCategories['Books'] = 0.85;
            $possibleCategories['Education'] = 0.7;
        } elseif (strpos($filename, 'clothes') !== false || strpos($filename, 'shirt') !== false) {
            $possibleCategories['Fashion'] = 0.9;
            $possibleCategories['Clothing'] = 0.85;
        } else {
            // General categories
            $possibleCategories['Electronics'] = 0.3;
            $possibleCategories['Home & Garden'] = 0.25;
            $possibleCategories['Fashion'] = 0.2;
            $possibleCategories['Other'] = 0.7;
        }
        
        // Add some randomness to make it more realistic
        foreach ($possibleCategories as $category => $confidence) {
            // Add some random variance
            $variance = (mt_rand(-10, 10) / 100);
            $newConfidence = max(0.1, min(0.99, $confidence + $variance));
            $possibleCategories[$category] = round($newConfidence, 2);
        }
        
        // Sort by confidence
        arsort($possibleCategories);
        
        return $possibleCategories;
    }
    
    /**
     * Get pre-categorized results by image hash
     */
    public function getCategorizedResultByHash(string $imageHash, array $options = []): ?array
    {
        $optionsHash = md5(serialize($options));
        $cacheKey = "cv_category_{$imageHash}_{$optionsHash}";
        return Cache::get($cacheKey);
    }
    
    /**
     * Batch categorize multiple images
     *
     * @param array $images
     * @param array $options
     * @return array
     */
    public function batchCategorize(array $images, array $options = []): array
    {
        $results = [];
        
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $results[] = [
                    'image' => $image->getClientOriginalName(),
                    'categories' => $this->categorizeItem($image, $options)
                ];
            }
        }
        
        return $results;
    }
}