<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AI\AIServicesController;

// AI Services Routes
Route::prefix('ai')->group(function () {
    // Product Description Generation
    Route::post('/generate-description', [AIServicesController::class, 'generateDescription']);
    
    // Image Enhancement
    Route::post('/enhance-image', [AIServicesController::class, 'enhanceImage']);
    Route::post('/remove-background', [AIServicesController::class, 'removeBackground']);
    
    // Computer Vision Categorization
    Route::post('/categorize-item', [AIServicesController::class, 'categorizeItem']);
    Route::post('/batch-categorize', [AIServicesController::class, 'batchCategorize']);
});