<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\ProductDescriptionGeneratorService;
use App\Services\AI\ImageEnhancementService;
use App\Services\AI\ComputerVisionCategorizationService;
use App\Services\MySqlToSqliteCacheService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AIServicesTest extends TestCase
{
    public function test_product_description_service_can_be_resolved()
    {
        $service = $this->app->make(ProductDescriptionGeneratorService::class);

        $this->assertInstanceOf(ProductDescriptionGeneratorService::class, $service);
    }

    public function test_image_enhancement_service_can_be_resolved()
    {
        $service = $this->app->make(ImageEnhancementService::class);

        $this->assertInstanceOf(ImageEnhancementService::class, $service);
    }

    public function test_computer_vision_service_can_be_resolved()
    {
        $service = $this->app->make(ComputerVisionCategorizationService::class);

        $this->assertInstanceOf(ComputerVisionCategorizationService::class, $service);
    }
    
    public function test_services_have_expected_methods()
    {
        $descService = $this->app->make(ProductDescriptionGeneratorService::class);
        $enhanceService = $this->app->make(ImageEnhancementService::class);
        $cvService = $this->app->make(ComputerVisionCategorizationService::class);
        
        // Check Product Description Service methods
        $this->assertTrue(method_exists($descService, 'generateDescriptionFromImage'));
        $this->assertTrue(method_exists($descService, 'generateMultipleDescriptions'));
        
        // Check Image Enhancement Service methods
        $this->assertTrue(method_exists($enhanceService, 'enhanceImage'));
        $this->assertTrue(method_exists($enhanceService, 'removeBackground'));
        $this->assertTrue(method_exists($enhanceService, 'smartEnhance'));
        
        // Check Computer Vision Service methods
        $this->assertTrue(method_exists($cvService, 'categorizeItem'));
        $this->assertTrue(method_exists($cvService, 'getPrimaryCategory'));
        $this->assertTrue(method_exists($cvService, 'findMatchingCategories'));
        $this->assertTrue(method_exists($cvService, 'suggestNewCategories'));
    }
}