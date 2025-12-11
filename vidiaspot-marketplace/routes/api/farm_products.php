<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FarmProductController;
use App\Http\Controllers\Api\FarmProductReportsController;

/*
|--------------------------------------------------------------------------
| Farm Product API Routes
|--------------------------------------------------------------------------
|
| API routes for farm product management and farm-specific features
|
*/

// Farm Product routes
Route::middleware(['auth:sanctum'])->prefix('farm-products')->group(function () {
    Route::apiResource('/', FarmProductController::class);
    Route::get('/my-farm-products', [FarmProductController::class, 'myFarmProducts']);
    Route::get('/analytics', [FarmProductController::class, 'analytics']);
});

// Public farm product routes
Route::get('/farm-products', [FarmProductController::class, 'index']);
Route::get('/farm-products/{id}', [FarmProductController::class, 'show'])->where('id', '[0-9]+');

// Farm Product Report routes
Route::middleware(['auth:sanctum'])->prefix('farm-product-reports')->group(function () {
    Route::get('/performance-summary', [FarmProductReportsController::class, 'performanceSummary']);
    Route::get('/farmer-productivity', [FarmProductReportsController::class, 'farmerProductivity']);
    Route::get('/seasonal-performance', [FarmProductReportsController::class, 'seasonalPerformance']);
    Route::get('/sustainability-report', [FarmProductReportsController::class, 'sustainabilityReport']);
    Route::get('/location-report', [FarmProductReportsController::class, 'locationReport']);
    Route::get('/top-products', [FarmProductReportsController::class, 'topPerformingProducts']);
    Route::get('/trends', [FarmProductReportsController::class, 'productivityTrends']);
});

// Additional farm-specific routes
Route::get('/farm-products/search', [FarmProductController::class, 'search']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/farm-products/my-favorites', [FarmProductController::class, 'myFavorites']);
    Route::post('/farm-products/{id}/favorite', [FarmProductController::class, 'toggleFavorite']);
    Route::post('/farm-products/{id}/contact-farmer', [FarmProductController::class, 'contactFarmer']);
});