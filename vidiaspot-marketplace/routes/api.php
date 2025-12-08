<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\PaymentController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::apiResource('categories', CategoryController::class);
Route::apiResource('ads', AdController::class);

// Public content pages routes
Route::get('/pages', [\App\Http\Controllers\Api\ContentPagesController::class, 'index']);
Route::get('/pages/{slug}', [\App\Http\Controllers\Api\ContentPagesController::class, 'show']);
Route::get('/about', [\App\Http\Controllers\Api\ContentPagesController::class, 'about']);
Route::get('/contact', [\App\Http\Controllers\Api\ContentPagesController::class, 'contact']);
Route::get('/services', [\App\Http\Controllers\Api\ContentPagesController::class, 'services']);
Route::get('/privacy', [\App\Http\Controllers\Api\ContentPagesController::class, 'privacy']);
Route::get('/terms', [\App\Http\Controllers\Api\ContentPagesController::class, 'terms']);

// Social Authentication Routes
Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
    Route::get('/providers', [SocialAuthController::class, 'getProviders']);
});

// Payment routes
Route::prefix('payment')->group(function () {
    // Initialize payment
    Route::post('/initialize', [PaymentController::class, 'initializePayment']);

    // Verify payment
    Route::post('/verify', [PaymentController::class, 'verifyPayment']);

    // Get transaction details
    Route::get('/transaction', [PaymentController::class, 'getTransaction']);

    // Webhook endpoints (no auth needed as these are called by payment providers)
    Route::post('/webhook/paystack', [PaymentController::class, 'handlePaystackWebhook'])->withoutMiddleware(['auth:sanctum']);
    Route::post('/webhook/flutterwave', [PaymentController::class, 'handleFlutterwaveWebhook'])->withoutMiddleware(['auth:sanctum']);
});

// Subscription routes
Route::prefix('subscription')->middleware(['auth:sanctum'])->group(function () {
    // Get available subscription plans
    Route::get('/', [SubscriptionController::class, 'index']);

    // Subscribe to a plan
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);

    // Get current subscription
    Route::get('/current', [SubscriptionController::class, 'getCurrentSubscription']);

    // Check subscription status
    Route::get('/status', [SubscriptionController::class, 'checkStatus']);

    // Get subscription benefits
    Route::get('/benefits', [SubscriptionController::class, 'getBenefits']);

    // Cancel subscription
    Route::delete('/cancel', [SubscriptionController::class, 'cancel']);

    // Renew subscription
    Route::post('/renew', [SubscriptionController::class, 'renew']);

    // Process successful payment callback
    Route::post('/process-payment', [SubscriptionController::class, 'processSuccessfulPayment'])->withoutMiddleware(['auth:sanctum']);
});

// Recommendation routes (both public and protected)
Route::get('/recommendations/trending', [RecommendationController::class, 'getTrendingAds']);
Route::get('/ads/{id}/similar', [RecommendationController::class, 'getSimilarAds']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Protected recommendation routes
    Route::get('/recommendations', [RecommendationController::class, 'getRecommendedAds']);

    // Ad routes
    Route::get('/my-ads', [AdController::class, 'myAds']);
    Route::post('/ads/{id}/images', [AdController::class, 'addImages']);

    // Category routes
    Route::get('/categories/tree', [CategoryController::class, 'tree']);

    // Message routes
    Route::apiResource('messages', MessageController::class);
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    Route::put('/messages/{id}/mark-as-read', [MessageController::class, 'markAsRead']);
});

// Admin routes (protected)
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    // Admin dashboard routes
    Route::get('/dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'dashboard']);
    Route::get('/analytics', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'analytics']);

    // Admin ads management routes
    Route::apiResource('ads', \App\Http\Controllers\Api\Admin\AdsController::class);
    Route::put('/ads/{id}/status', [\App\Http\Controllers\Api\Admin\AdsController::class, 'updateStatus']);
    Route::get('/ads/pending', [\App\Http\Controllers\Api\Admin\AdsController::class, 'pending']);

    // Admin users management routes
    Route::apiResource('users', \App\Http\Controllers\Api\Admin\UsersController::class);
    Route::patch('/users/{id}/role', [\App\Http\Controllers\Api\Admin\UsersController::class, 'assignRole']);
    Route::get('/users/stats', [\App\Http\Controllers\Api\Admin\UsersController::class, 'stats']);

    // Admin categories management routes
    Route::apiResource('categories', \App\Http\Controllers\Api\Admin\CategoriesController::class);
    Route::patch('/categories/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\CategoriesController::class, 'toggleStatus']);

    // Admin payments management routes
    Route::get('/payments', [\App\Http\Controllers\Api\Admin\PaymentsController::class, 'index']);
    Route::get('/payments/{id}', [\App\Http\Controllers\Api\Admin\PaymentsController::class, 'show']);
    Route::put('/payments/{id}/status', [\App\Http\Controllers\Api\Admin\PaymentsController::class, 'updateStatus']);
    Route::get('/payments/stats', [\App\Http\Controllers\Api\Admin\PaymentsController::class, 'stats']);

    // Admin subscriptions management routes
    Route::apiResource('subscriptions', \App\Http\Controllers\Api\Admin\SubscriptionsController::class);
    Route::patch('/subscriptions/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\SubscriptionsController::class, 'toggleStatus']);
    Route::get('/subscriptions/stats', [\App\Http\Controllers\Api\Admin\SubscriptionsController::class, 'stats']);

    // Admin vendors management routes
    Route::apiResource('vendors', \App\Http\Controllers\Api\Admin\VendorsController::class);
    Route::patch('/vendors/{id}/approve', [\App\Http\Controllers\Api\Admin\VendorsController::class, 'approve']);
    Route::patch('/vendors/{id}/reject', [\App\Http\Controllers\Api\Admin\VendorsController::class, 'reject']);
    Route::patch('/vendors/{id}/suspend', [\App\Http\Controllers\Api\Admin\VendorsController::class, 'suspend']);
    Route::patch('/vendors/{id}/toggle-verification', [\App\Http\Controllers\Api\Admin\VendorsController::class, 'toggleVerification']);
    Route::patch('/vendors/{id}/toggle-featured', [\App\Http\Controllers\Api\Admin\VendorsController::class, 'toggleFeatured']);
    Route::get('/vendors/stats', [\App\Http\Controllers\Api\Admin\VendorsController::class, 'stats']);

    // Admin locations management routes
    Route::get('/locations/countries', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'getCountries']);
    Route::post('/locations/countries', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'storeCountry']);
    Route::put('/locations/countries/{id}', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'updateCountry']);
    Route::patch('/locations/countries/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'toggleCountryStatus']);

    Route::get('/locations/states/{countryId}', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'getStates']);
    Route::post('/locations/states/{countryId}', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'storeState']);
    Route::put('/locations/states/{id}', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'updateState']);
    Route::patch('/locations/states/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'toggleStateStatus']);

    Route::get('/locations/cities/{stateId}', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'getCities']);
    Route::post('/locations/cities/{stateId}', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'storeCity']);
    Route::put('/locations/cities/{id}', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'updateCity']);
    Route::patch('/locations/cities/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'toggleCityStatus']);

    Route::get('/locations/stats', [\App\Http\Controllers\Api\Admin\LocationsController::class, 'stats']);

    // Admin featured ads management routes
    Route::apiResource('featured-ads', \App\Http\Controllers\Api\Admin\FeaturedAdsController::class);
    Route::patch('/featured-ads/{id}/cancel', [\App\Http\Controllers\Api\Admin\FeaturedAdsController::class, 'cancel']);
    Route::patch('/featured-ads/{id}/extend', [\App\Http\Controllers\Api\Admin\FeaturedAdsController::class, 'extend']);
    Route::get('/featured-ads/stats', [\App\Http\Controllers\Api\Admin\FeaturedAdsController::class, 'stats']);

    // Admin blogs management routes
    Route::apiResource('blogs', \App\Http\Controllers\Api\Admin\BlogsController::class);
    Route::patch('/blogs/{id}/publish', [\App\Http\Controllers\Api\Admin\BlogsController::class, 'publish']);
    Route::patch('/blogs/{id}/unpublish', [\App\Http\Controllers\Api\Admin\BlogsController::class, 'unpublish']);
    Route::patch('/blogs/{id}/toggle-featured', [\App\Http\Controllers\Api\Admin\BlogsController::class, 'toggleFeatured']);
    Route::get('/blogs/stats', [\App\Http\Controllers\Api\Admin\BlogsController::class, 'stats']);

    // Admin payment gateways management routes
    Route::get('/payments/gateways', [\App\Http\Controllers\Api\Admin\PaymentGatewaysController::class, 'index']);
    Route::put('/payments/gateways/{gateway}', [\App\Http\Controllers\Api\Admin\PaymentGatewaysController::class, 'update']);
    Route::patch('/payments/gateways/{gateway}/toggle-status', [\App\Http\Controllers\Api\Admin\PaymentGatewaysController::class, 'toggleStatus']);
    Route::get('/payments/gateways/support', [\App\Http\Controllers\Api\Admin\PaymentGatewaysController::class, 'supported']);

    // Admin testimonials management routes
    Route::apiResource('testimonials', \App\Http\Controllers\Api\Admin\TestimonialsController::class);
    Route::patch('/testimonials/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\TestimonialsController::class, 'toggleStatus']);
    Route::patch('/testimonials/{id}/toggle-featured', [\App\Http\Controllers\Api\Admin\TestimonialsController::class, 'toggleFeatured']);

    // Admin careers management routes
    Route::apiResource('careers', \App\Http\Controllers\Api\Admin\CareersController::class);
    Route::patch('/careers/{id}/publish', [\App\Http\Controllers\Api\Admin\CareersController::class, 'publish']);
    Route::patch('/careers/{id}/unpublish', [\App\Http\Controllers\Api\Admin\CareersController::class, 'unpublish']);

    // Admin ad placements management routes
    Route::apiResource('ad-placements', \App\Http\Controllers\Api\Admin\AdPlacementsController::class);
    Route::patch('/ad-placements/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\AdPlacementsController::class, 'toggleStatus']);
    Route::get('/ad-placements/stats', [\App\Http\Controllers\Api\Admin\AdPlacementsController::class, 'stats']);

    // Admin premium ads management routes
    Route::apiResource('premium-ads', \App\Http\Controllers\Api\Admin\PremiumAdsController::class);
    Route::patch('/premium-ads/{id}/activate', [\App\Http\Controllers\Api\Admin\PremiumAdsController::class, 'activate']);
    Route::patch('/premium-ads/{id}/pause', [\App\Http\Controllers\Api\Admin\PremiumAdsController::class, 'pause']);
    Route::patch('/premium-ads/{id}/cancel', [\App\Http\Controllers\Api\Admin\PremiumAdsController::class, 'cancel']);
    Route::get('/premium-ads/stats', [\App\Http\Controllers\Api\Admin\PremiumAdsController::class, 'stats']);

    // Admin content pages management routes
    Route::apiResource('content-pages', \App\Http\Controllers\Api\Admin\ContentPagesController::class);
    Route::patch('/content-pages/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\ContentPagesController::class, 'toggleStatus']);
});
