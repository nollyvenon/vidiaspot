<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\ProductDescriptionGeneratorService;
use App\Services\AI\ImageEnhancementService;
use App\Services\AI\ComputerVisionCategorizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AIServicesController extends Controller
{
    protected $descriptionService;
    protected $enhancementService;
    protected $categorizationService;
    
    public function __construct(
        ProductDescriptionGeneratorService $descriptionService,
        ImageEnhancementService $enhancementService,
        ComputerVisionCategorizationService $categorizationService
    ) {
        $this->descriptionService = $descriptionService;
        $this->enhancementService = $enhancementService;
        $this->categorizationService = $categorizationService;
    }
    
    /**
     * Generate product description from image
     */
    public function generateDescription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'language' => 'sometimes|string|in:en,es,fr,de,pt,ru,ja,zh',
            'type' => 'sometimes|string|in:basic,detailed,marketing'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        try {
            $image = $request->file('image');
            $language = $request->get('language', 'en');
            $type = $request->get('type', 'basic');
            
            if ($type === 'multiple') {
                $descriptions = $this->descriptionService->generateMultipleDescriptions($image, $language);
                return response()->json([
                    'success' => true,
                    'data' => $descriptions
                ]);
            } else {
                $description = $this->descriptionService->generateDescriptionFromImage($image, $language);
                return response()->json([
                    'success' => true,
                    'data' => [
                        'description' => $description
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate description: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Enhance image
     */
    public function enhanceImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'enhancement_type' => 'sometimes|string|in:brightness,contrast,saturation,sharpness,resize,smart',
            'brightness' => 'sometimes|integer|min:-100|max:100',
            'contrast' => 'sometimes|integer|min:-100|max:100',
            'saturation' => 'sometimes|integer|min:-100|max:100',
            'sharpness' => 'sometimes|integer|min:0|max:100',
            'resize_width' => 'sometimes|integer|min:10|max:4000',
            'resize_height' => 'sometimes|integer|min:10|max:4000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        try {
            $image = $request->file('image');
            $enhancementType = $request->get('enhancement_type', 'smart');
            
            $options = [];
            if ($request->has('brightness')) $options['brightness'] = $request->get('brightness');
            if ($request->has('contrast')) $options['contrast'] = $request->get('contrast');
            if ($request->has('saturation')) $options['saturation'] = $request->get('saturation');
            if ($request->has('sharpness')) $options['sharpness'] = $request->get('sharpness');
            if ($request->has('resize_width') && $request->has('resize_height')) {
                $options['resize_width'] = $request->get('resize_width');
                $options['resize_height'] = $request->get('resize_height');
            }
            
            $enhancedImagePath = match($enhancementType) {
                'smart' => $this->enhancementService->smartEnhance($image),
                default => $this->enhancementService->enhanceImage($image, $options)
            };
            
            return response()->json([
                'success' => true,
                'data' => [
                    'enhanced_image_path' => $enhancedImagePath,
                    'download_url' => asset('storage/' . $enhancedImagePath)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to enhance image: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Remove background from image
     */
    public function removeBackground(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        try {
            $image = $request->file('image');
            
            $resultPath = $this->enhancementService->removeBackground($image);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'image_path' => $resultPath,
                    'download_url' => asset('storage/' . $resultPath)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to remove background: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Categorize item using computer vision
     */
    public function categorizeItem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        try {
            $image = $request->file('image');
            
            $categories = $this->categorizationService->categorizeItem($image);
            $primaryCategory = $this->categorizationService->getPrimaryCategory($image);
            $matchingCategories = $this->categorizationService->findMatchingCategories($image);
            $suggestedCategories = $this->categorizationService->suggestNewCategories($image);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'primary_category' => $primaryCategory,
                    'all_categories' => $categories,
                    'matching_categories' => $matchingCategories,
                    'suggested_categories' => $suggestedCategories
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to categorize item: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Batch categorize multiple images
     */
    public function batchCategorize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        try {
            $images = $request->file('images');
            
            $results = $this->categorizationService->batchCategorize($images);
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to batch categorize: ' . $e->getMessage()], 500);
        }
    }
}