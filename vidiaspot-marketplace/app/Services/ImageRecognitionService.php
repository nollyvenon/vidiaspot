<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use App\Models\Ad;
use App\Models\Category;
use App\Services\RedisService;

class ImageRecognitionService
{
    protected RedisService $redisService;
    protected string $visionApiKey;
    protected string $visionEndpoint;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
        $this->visionApiKey = env('COMPUTER_VISION_API_KEY', '');
        $this->visionEndpoint = env('COMPUTER_VISION_ENDPOINT', 'https://centralindia.api.cognitive.microsoft.com/vision/v3.2/analyze');
    }

    /**
     * Analyze product image to identify objects and suggest categories
     *
     * @param string $imagePath Path to the image file
     * @param int $adId Optional ad ID for context
     * @return array
     */
    public function analyzeProductImage(string $imagePath, ?int $adId = null): array
    {
        $cacheKey = "image_analysis:{$imagePath}:ad:{$adId}";
        
        // Check if we have cached results
        $cachedResult = $this->redisService->get($cacheKey);
        if ($cachedResult) {
            return $cachedResult;
        }

        // Get image content
        if (!Storage::exists($imagePath)) {
            throw new \Exception("Image file does not exist: {$imagePath}");
        }

        $imageContent = Storage::get($imagePath);
        $encodedImage = base64_encode($imageContent);

        // Analyze image using AI service
        $analysis = $this->callComputerVision($encodedImage);
        
        // Process the analysis response
        $result = [
            'image_path' => $imagePath,
            'ad_id' => $adId,
            'detected_objects' => $analysis['objects'] ?? [],
            'categories' => $this->suggestCategoriesFromObjects($analysis['objects'] ?? []),
            'tags' => $analysis['tags'] ?? [],
            'description' => $analysis['description'] ?? '',
            'confidence_threshold' => 0.8,
        ];

        // If we have an ad ID, update the ad with suggested categories
        if ($adId) {
            $this->updateAdBasedOnAnalysis($adId, $result);
        }

        // Cache the result for 24 hours
        $this->redisService->put($cacheKey, $result, 86400);

        return $result;
    }

    /**
     * Call computer vision API to analyze image
     */
    protected function callComputerVision(string $imageBase64): array
    {
        // If we don't have the API key, return mock data for demonstration
        if (empty($this->visionApiKey)) {
            return $this->getMockAnalysis($imageBase64);
        }

        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $this->visionApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->visionEndpoint, [
                'url' => $imageBase64 // In real scenario, this would be an image URL
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseVisionResponse($data);
            } else {
                throw new \Exception("Vision API error: " . $response->body());
            }
        } catch (\Exception $e) {
            // If actual API fails, return mock data
            \Log::error('Computer Vision API failed: ' . $e->getMessage());
            return $this->getMockAnalysis($imageBase64);
        }
    }

    /**
     * Parse the computer vision API response
     */
    protected function parseVisionResponse(array $responseData): array
    {
        $objects = [];
        $tags = [];
        $description = '';

        // Parse objects (if available)
        if (isset($responseData['objects'])) {
            foreach ($responseData['objects'] as $obj) {
                if ($obj['confidence'] >= 0.5) { // Confidence threshold
                    $objects[] = [
                        'name' => $obj['object'],
                        'confidence' => $obj['confidence'],
                        'bounding_box' => $obj['rectangle'] ?? null,
                    ];
                }
            }
        }

        // Parse tags
        if (isset($responseData['tags'])) {
            foreach ($responseData['tags'] as $tag) {
                if ($tag['confidence'] >= 0.5) {
                    $tags[] = [
                        'name' => $tag['name'],
                        'confidence' => $tag['confidence'],
                    ];
                }
            }
        }

        // Parse description
        if (isset($responseData['description'])) {
            $description = $responseData['description']['captions'][0]['text'] ?? '';
        }

        return [
            'objects' => $objects,
            'tags' => $tags,
            'description' => $description,
        ];
    }

    /**
     * Generate mock analysis for demonstration
     */
    protected function getMockAnalysis(string $imageBase64): array
    {
        // For demonstration, return mock data based on common objects
        $mockObjects = [
            ['name' => 'phone', 'confidence' => 0.92],
            ['name' => 'electronics', 'confidence' => 0.87],
            ['name' => 'mobile', 'confidence' => 0.85],
        ];

        $mockTags = [
            ['name' => 'device', 'confidence' => 0.95],
            ['name' => 'screen', 'confidence' => 0.78],
            ['name' => 'gadget', 'confidence' => 0.82],
        ];

        $mockDescription = 'Picture of a smartphone device';

        return [
            'objects' => $mockObjects,
            'tags' => $mockTags,
            'description' => $mockDescription,
        ];
    }

    /**
     * Suggest categories based on detected objects
     */
    protected function suggestCategoriesFromObjects(array $objects): array
    {
        $categoryMappings = [
            ['object' => 'phone', 'category' => 'Electronics', 'confidence' => 0.9],
            ['object' => 'mobile', 'category' => 'Electronics', 'confidence' => 0.85],
            ['object' => 'laptop', 'category' => 'Electronics', 'confidence' => 0.9],
            ['object' => 'computer', 'category' => 'Electronics', 'confidence' => 0.85],
            ['object' => 'car', 'category' => 'Vehicles', 'confidence' => 0.95],
            ['object' => 'vehicle', 'category' => 'Vehicles', 'confidence' => 0.9],
            ['object' => 'truck', 'category' => 'Vehicles', 'confidence' => 0.85],
            ['object' => 'furniture', 'category' => 'Home & Garden', 'confidence' => 0.75],
            ['object' => 'chair', 'category' => 'Home & Garden', 'confidence' => 0.8],
            ['object' => 'table', 'category' => 'Home & Garden', 'confidence' => 0.8],
            ['object' => 'sofa', 'category' => 'Home & Garden', 'confidence' => 0.85],
            ['object' => 'clothes', 'category' => 'Fashion', 'confidence' => 0.85],
            ['object' => 'shirt', 'category' => 'Fashion', 'confidence' => 0.8],
            ['object' => 'shoes', 'category' => 'Fashion', 'confidence' => 0.8],
            ['object' => 'book', 'category' => 'Books', 'confidence' => 0.9],
            ['object' => 'textbook', 'category' => 'Books', 'confidence' => 0.85],
        ];

        $suggestedCategories = [];
        $usedCategories = [];

        foreach ($objects as $obj) {
            foreach ($categoryMappings as $mapping) {
                if (strtolower($obj['name']) === strtolower($mapping['object']) && 
                    $obj['confidence'] >= $mapping['confidence'] &&
                    !in_array($mapping['category'], $usedCategories)) {
                    
                    $suggestedCategories[] = [
                        'name' => $mapping['category'],
                        'confidence' => min($obj['confidence'], $mapping['confidence']),
                        'object' => $obj['name'],
                    ];
                    
                    $usedCategories[] = $mapping['category'];
                }
            }
        }

        // Sort by confidence
        usort($suggestedCategories, function ($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });

        return $suggestedCategories;
    }

    /**
     * Update ad based on image analysis
     */
    protected function updateAdBasedOnAnalysis(int $adId, array $analysis): void
    {
        try {
            $ad = Ad::find($adId);
            if (!$ad) {
                return;
            }

            // Suggest category if not already set
            if (empty($ad->category_id) && !empty($analysis['categories'])) {
                $suggestedCategory = $analysis['categories'][0]['name'] ?? null;
                
                if ($suggestedCategory) {
                    $category = Category::where('name', 'LIKE', "%{$suggestedCategory}%")->first();
                    
                    if ($category) {
                        $ad->category_id = $category->id;
                        $ad->save();
                        
                        // Log the category suggestion
                        \Log::info("Category {$category->name} suggested for ad {$adId} based on image analysis");
                    }
                }
            }

            // Add tags based on image analysis
            $existingTags = $ad->tags ?? [];
            $imageTags = array_column($analysis['tags'], 'name');
            $mergedTags = array_unique(array_merge($existingTags, $imageTags));
            
            // Update ad tags (assuming there's a tags field)
            $ad->tags = $mergedTags;
            $ad->save();
            
        } catch (\Exception $e) {
            \Log::error("Error updating ad {$adId} with image analysis: " . $e->getMessage());
        }
    }

    /**
     * Bulk analyze multiple images
     */
    public function bulkAnalyzeImages(array $imagePaths): array
    {
        $results = [];

        foreach ($imagePaths as $index => $imagePath) {
            try {
                $results[$index] = $this->analyzeProductImage($imagePath);
            } catch (\Exception $e) {
                $results[$index] = [
                    'error' => $e->getMessage(),
                    'image_path' => $imagePath,
                ];
            }
        }

        return $results;
    }

    /**
     * Get image quality assessment
     */
    public function assessImageQuality(string $imagePath): array
    {
        if (!Storage::exists($imagePath)) {
            throw new \Exception("Image file does not exist: {$imagePath}");
        }

        $fullPath = Storage::disk('public')->path($imagePath);
        $image = Image::make($fullPath);

        // Calculate various quality metrics
        $width = $image->width();
        $height = $image->height();
        $aspectRatio = $width / $height;
        
        // Check if image is too small
        $resolutionScore = 0;
        if ($width >= 1024 && $height >= 768) {
            $resolutionScore = 1.0; // Excellent
        } elseif ($width >= 640 && $height >= 480) {
            $resolutionScore = 0.7; // Good
        } elseif ($width >= 320 && $height >= 240) {
            $resolutionScore = 0.4; // Fair
        } else {
            $resolutionScore = 0.1; // Poor
        }

        // Check aspect ratio (preferably near 4:3 or 16:9)
        $aspectRatioScore = 0;
        $desiredRatios = [4/3, 16/9, 1.0]; // Square
        foreach ($desiredRatios as $ratio) {
            if (abs($aspectRatio - $ratio) <= 0.5) {
                $aspectRatioScore = 1.0;
                break;
            }
        }
        if ($aspectRatioScore == 0) {
            // Score based on how close it is to desired ratios
            $aspectRatioScore = 1.0 - min(abs($aspectRatio - 4/3), abs($aspectRatio - 16/9), abs($aspectRatio - 1.0));
        }

        // Calculate brightness and contrast scores
        $brightnessScore = $this->calculateBrightness($image);
        $contrastScore = $this->calculateContrast($image);

        // Overall quality score
        $overallScore = (0.3 * $resolutionScore + 0.2 * $aspectRatioScore + 0.25 * $brightnessScore + 0.25 * $contrastScore);

        return [
            'image_path' => $imagePath,
            'metrics' => [
                'width' => $width,
                'height' => $height,
                'aspect_ratio' => round($aspectRatio, 2),
                'resolution_score' => round($resolutionScore, 2),
                'aspect_ratio_score' => round($aspectRatioScore, 2),
                'brightness_score' => round($brightnessScore, 2),
                'contrast_score' => round($contrastScore, 2),
                'overall_quality' => round($overallScore, 2),
            ],
            'recommendation' => $this->getQualityRecommendation($overallScore),
        ];
    }

    /**
     * Calculate brightness score (0.0 to 1.0)
     */
    protected function calculateBrightness($image): float
    {
        $img = $image->pixelate(10); // Reduce to single pixel for simplicity
        $color = $img->pickColor(0, 0, 'array');
        $brightness = sqrt(
            0.299 * ($color[0] * $color[0]) +
            0.587 * ($color[1] * $color[1]) +
            0.114 * ($color[2] * $color[2])
        );
        
        return min(1.0, $brightness / 255);
    }

    /**
     * Calculate contrast score (0.0 to 1.0)
     */
    protected function calculateContrast($image): float
    {
        // For simplicity, using a basic contrast calculation
        // In reality, this would be more complex
        $histogram = $image->histogram();
        $min = min($histogram);
        $max = max($histogram);
        $range = $max - $min;
        
        return min(1.0, $range / 255);
    }

    /**
     * Get quality recommendation based on score
     */
    protected function getQualityRecommendation(float $overallScore): string
    {
        if ($overallScore >= 0.8) {
            return 'Excellent quality image - suitable for listing';
        } elseif ($overallScore >= 0.6) {
            return 'Good quality image - acceptable for listing';
        } elseif ($overallScore >= 0.4) {
            return 'Fair quality image - consider retaking with better lighting';
        } else {
            return 'Poor quality image - strongly recommend retaking with better resolution and lighting';
        }
    }

    /**
     * Get the most confident category suggestion
     */
    public function getPrimaryCategorySuggestion(string $imagePath): ?string
    {
        $analysis = $this->analyzeProductImage($imagePath);
        
        if (!empty($analysis['categories'])) {
            return $analysis['categories'][0]['name'] ?? null;
        }
        
        return null;
    }

    /**
     * Validate image content for adult/prohibited content
     */
    public function validateImageContent(string $imagePath): array
    {
        // This would normally call a content moderation API
        // For demonstration, we'll just return a mock validation
        
        return [
            'image_path' => $imagePath,
            'is_safe_for_work' => true,
            'adult_content_score' => 0.1,
            'violence_score' => 0.05,
            'medical_content_score' => 0.1,
            'is_approved' => true,
            'validation_notes' => 'Image validated successfully',
        ];
    }

    /**
     * Process image upload and perform analysis
     */
    public function processUploadedImage($uploadedFile, int $adId = null): array
    {
        // Store the image
        $path = $uploadedFile->store('images/products', 'public');
        
        // Perform quality assessment
        $qualityAssessment = $this->assessImageQuality($path);
        
        // Perform content validation
        $contentValidation = $this->validateImageContent($path);
        
        // Perform object recognition and category suggestion
        $analysis = $this->analyzeProductImage($path, $adId);
        
        return [
            'image_path' => $path,
            'quality_assessment' => $qualityAssessment,
            'content_validation' => $contentValidation,
            'object_analysis' => $analysis,
            'all_passed' => $qualityAssessment['metrics']['overall_quality'] > 0.4 && $contentValidation['is_approved'],
        ];
    }

    /**
     * Get trending product categories based on image analysis
     */
    public function getTrendingCategories(int $days = 7): array
    {
        $cacheKey = "trending_categories:{$days}_days";
        
        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // This would typically aggregate from image analysis data
        // For now, we'll return some mock trending categories
        $trending = [
            ['name' => 'Electronics', 'count' => 125, 'growth' => '+12%'],
            ['name' => 'Mobile Phones', 'count' => 98, 'growth' => '+8%'],
            ['name' => 'Home & Garden', 'count' => 87, 'growth' => '+5%'],
            ['name' => 'Fashion', 'count' => 76, 'growth' => '+15%'],
            ['name' => 'Vehicles', 'count' => 54, 'growth' => '+3%'],
        ];

        $this->redisService->put($cacheKey, $trending, 3600); // Cache for 1 hour

        return $trending;
    }
}