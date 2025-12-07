<?php

namespace App\Http\Controllers;

use App\Services\ImageRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;

class ImageRecognitionController extends Controller
{
    protected ImageRecognitionService $imageRecognitionService;

    public function __construct(ImageRecognitionService $imageRecognitionService)
    {
        $this->imageRecognitionService = $imageRecognitionService;
    }

    /**
     * Analyze a product image to suggest categories
     */
    public function analyzeImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'ad_id' => 'nullable|integer|exists:ads,id',
        ]);

        $image = $request->file('image');
        $adId = $request->input('ad_id');

        // Store the image temporarily
        $path = $image->store('temp', 'public');

        try {
            $analysis = $this->imageRecognitionService->analyzeProductImage($path, $adId);

            return response()->json([
                'success' => true,
                'data' => $analysis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assess image quality
     */
    public function assessImageQuality(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $image = $request->file('image');

        // Store the image temporarily
        $path = $image->store('temp', 'public');

        try {
            $quality = $this->imageRecognitionService->assessImageQuality($path);

            return response()->json([
                'success' => true,
                'data' => $quality,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process uploaded image with full analysis
     */
    public function processImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
            'ad_id' => 'nullable|integer|exists:ads,id',
        ]);

        $image = $request->file('image');
        $adId = $request->input('ad_id');

        try {
            $result = $this->imageRecognitionService->processUploadedImage($image, $adId);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get primary category suggestion for an image
     */
    public function getCategorySuggestion(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $image = $request->file('image');

        // Store the image temporarily
        $path = $image->store('temp', 'public');

        try {
            $suggestion = $this->imageRecognitionService->getPrimaryCategorySuggestion($path);

            return response()->json([
                'success' => true,
                'suggested_category' => $suggestion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate image content
     */
    public function validateImageContent(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $image = $request->file('image');

        // Store the image temporarily
        $path = $image->store('temp', 'public');

        try {
            $validation = $this->imageRecognitionService->validateImageContent($path);

            return response()->json([
                'success' => true,
                'validation' => $validation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk analyze multiple images
     */
    public function bulkAnalyze(Request $request): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $images = $request->file('images');
        $storedPaths = [];

        // Store all images temporarily
        foreach ($images as $image) {
            $storedPaths[] = $image->store('temp', 'public');
        }

        try {
            $results = $this->imageRecognitionService->bulkAnalyzeImages($storedPaths);

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get trending categories based on image analysis
     */
    public function getTrendingCategories(Request $request): JsonResponse
    {
        $days = $request->input('days', 7);

        $trending = $this->imageRecognitionService->getTrendingCategories($days);

        return response()->json([
            'success' => true,
            'data' => $trending,
            'days' => $days,
        ]);
    }

    /**
     * Get image analysis history for an ad
     */
    public function getImageAnalysisHistory(Request $request, int $adId): JsonResponse
    {
        // This would typically return data from a historical database
        // For now, we'll return mock data

        $history = [
            [
                'analysis_id' => 1,
                'image_path' => 'images/products/product1.jpg',
                'detected_objects' => ['phone', 'electronics'],
                'suggested_category' => 'Electronics',
                'confidence' => 0.92,
                'analyzed_at' => '2023-01-15 10:30:00',
                'quality_score' => 0.85,
            ],
            [
                'analysis_id' => 2,
                'image_path' => 'images/products/product1_v2.jpg',
                'detected_objects' => ['mobile', 'device'],
                'suggested_category' => 'Electronics',
                'confidence' => 0.88,
                'analyzed_at' => '2023-01-16 09:15:00',
                'quality_score' => 0.92,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $history,
            'ad_id' => $adId,
            'total_analyses' => count($history),
        ]);
    }

    /**
     * Compare two images
     */
    public function compareImages(Request $request): JsonResponse
    {
        $request->validate([
            'image1' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
            'image2' => 'required|file|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $image1 = $request->file('image1');
        $image2 = $request->file('image2');

        // Store images temporarily
        $path1 = $image1->store('temp', 'public');
        $path2 = $image2->store('temp', 'public');

        // For now, return mock comparison data
        // In a real system, this would use computer vision to compare images
        $comparison = [
            'similarity_score' => 0.85, // 85% similarity
            'image1_analysis' => $this->imageRecognitionService->analyzeProductImage($path1),
            'image2_analysis' => $this->imageRecognitionService->analyzeProductImage($path2),
            'common_objects' => ['electronics', 'device'],
            'differences' => [
                'image1_has' => ['phone_case'],
                'image2_has' => ['charger'],
            ],
        ];

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
        ]);
    }

    /**
     * Get image analysis analytics for admin dashboard
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        // Mock analytics data
        $analytics = [
            'total_images_analyzed' => 1250,
            'successful_analyses' => 1235,
            'accuracy_rate' => 0.92,
            'most_common_categories' => [
                ['name' => 'Electronics', 'count' => 340],
                ['name' => 'Fashion', 'count' => 298],
                ['name' => 'Home & Garden', 'count' => 210],
                ['name' => 'Vehicles', 'count' => 156],
                ['name' => 'Books', 'count' => 95],
            ],
            'average_confidence' => 0.85,
            'quality_issues_detected' => 45,
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Re-analyze all images for an ad
     */
    public function reAnalyzeAdImages(Request $request, int $adId): JsonResponse
    {
        // This would typically fetch all images for the ad and re-analyze them
        // For now, we'll return mock data

        $result = [
            'ad_id' => $adId,
            'total_images' => 3,
            're_analyzed' => 3,
            'updated_categories' => 1, // Number of categories updated
            'quality_improvements' => 2, // Images with quality improvement suggestions
            'message' => 'Ad images re-analyzed successfully',
        ];

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}