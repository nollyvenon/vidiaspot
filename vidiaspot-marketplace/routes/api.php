<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\Auth\SocialAuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SubscriptionController;

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

// AI-powered features routes
Route::prefix('ai')->middleware(['auth:sanctum'])->group(function () {
    // Pricing recommendations
    Route::post('/pricing-recommendation', [AIController::class, 'getPricingRecommendation']);

    // Demand forecasting
    Route::get('/demand-forecast', [AIController::class, 'getDemandForecast']);

    // Success prediction
    Route::post('/success-prediction', [AIController::class, 'getSuccessPrediction']);

    // Duplicate detection
    Route::get('/check-duplicates/{ad_id}', [AIController::class, 'checkDuplicates']);

    // Fraud analysis
    Route::get('/fraud-analysis', [AIController::class, 'getFraudAnalysis']);
    Route::post('/fraud-analysis', [AIController::class, 'getFraudAnalysis']);

    // Smart recommendations
    Route::get('/recommendations', [AIController::class, 'getRecommendations']);

    // Seasonal trends
    Route::get('/seasonal-trends/{category_id}', [AIController::class, 'getSeasonalTrends']);

    // NEW: AI-Powered Image and Computer Vision Features
    // Product description generation from images
    Route::post('/generate-description', [AIServicesController::class, 'generateDescription']);

    // Image enhancement and background removal
    Route::post('/enhance-image', [AIServicesController::class, 'enhanceImage']);
    Route::post('/remove-background', [AIServicesController::class, 'removeBackground']);

    // Computer vision categorization
    Route::post('/categorize-item', [AIServicesController::class, 'categorizeItem']);
    Route::post('/batch-categorize', [AIServicesController::class, 'batchCategorize']);

    // NEW: Advanced Search & Discovery Features
    // Voice search with natural language processing
    Route::post('/voice-search', [AdvancedSearchDiscoveryController::class, 'voiceSearch']);

    // Visual search using image recognition
    Route::post('/visual-search', [AdvancedSearchDiscoveryController::class, 'visualSearch']);

    // Augmented Reality (AR) view for products
    Route::get('/ar-view/{adId}', [AdvancedSearchDiscoveryController::class, 'getARViewData']);
    Route::post('/ar-session/{adId}', [AdvancedSearchDiscoveryController::class, 'getARSessionData']);

    // Social search - find listings from friends' networks
    Route::post('/social-search', [AdvancedSearchDiscoveryController::class, 'socialSearch']);
    Route::get('/friend-recommendations', [AdvancedSearchDiscoveryController::class, 'getFriendRecommendations']);
    Route::get('/social-activity-feed', [AdvancedSearchDiscoveryController::class, 'getSocialActivityFeed']);

    // Trending and seasonal item recommendations
    Route::get('/trending-items', [AdvancedSearchDiscoveryController::class, 'getTrendingItems']);
    Route::get('/seasonal-recommendations', [AdvancedSearchDiscoveryController::class, 'getSeasonalRecommendations']);
    Route::get('/personalized-seasonal-recommendations', [AdvancedSearchDiscoveryController::class, 'getPersonalizedSeasonalRecommendations']);
    Route::get('/trend-forecast', [AdvancedSearchDiscoveryController::class, 'getTrendForecast']);

    // Price drop alerts for saved items
    Route::post('/price-alert', [AdvancedSearchDiscoveryController::class, 'createPriceAlert']);
    Route::get('/user-price-alerts/{userId}', [AdvancedSearchDiscoveryController::class, 'getUserPriceAlerts']);

    // Geographic heat maps for high-demand areas
    Route::get('/geographic-heat-map', [AdvancedSearchDiscoveryController::class, 'getGeographicHeatMap']);
    Route::get('/trending-locations/{categoryId}', [AdvancedSearchDiscoveryController::class, 'getTrendingLocationsForCategory']);
    Route::get('/seasonal-location-patterns', [AdvancedSearchDiscoveryController::class, 'getSeasonalLocationPatterns']);
    Route::get('/demand-forecast-locations', [AdvancedSearchDiscoveryController::class, 'getDemandForecastForLocations']);
});

// Personalization API routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/personalization/feed', [App\Http\Controllers\Api\PersonalizationController::class, 'getPersonalizedFeed']);
    Route::post('/personalization/behavior', [App\Http\Controllers\Api\PersonalizationController::class, 'trackBehavior']);
    Route::get('/personalization/preferences', [App\Http\Controllers\Api\PersonalizationController::class, 'getUserPreferences']);
    Route::put('/personalization/preferences', [App\Http\Controllers\Api\PersonalizationController::class, 'updateUserPreferences']);
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

    // Feature flags management routes (Admin)
    Route::apiResource('feature-flags', \App\Http\Controllers\Api\Admin\FeatureFlagsController::class);
    Route::patch('/feature-flags/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\FeatureFlagsController::class, 'toggleStatus']);
    Route::put('/feature-flags/{id}/region', [\App\Http\Controllers\Api\Admin\FeatureFlagsController::class, 'updateRegion']);
    Route::post('/feature-flags/check-availability', [\App\Http\Controllers\Api\Admin\FeatureFlagsController::class, 'checkAvailability']);

    // Insurance providers management routes (Admin)
    Route::apiResource('insurance-providers', \App\Http\Controllers\Api\Admin\InsuranceProvidersController::class);
    Route::patch('/insurance-providers/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\InsuranceProvidersController::class, 'toggleStatus']);

    // Store template management routes (Admin)
    Route::apiResource('store-templates', \App\Http\Controllers\Api\Admin\StoreTemplatesController::class);
    Route::patch('/store-templates/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\StoreTemplatesController::class, 'toggleStatus']);
    Route::get('/store-templates/active', [\App\Http\Controllers\Api\Admin\StoreTemplatesController::class, 'getActiveTemplates']);

    // Vendor store management routes
    Route::post('/vendor-store/custom-field-templates', [\App\Http\Controllers\VendorStoreController::class, 'getCustomFieldTemplates']);
    Route::post('/ads/{adId}/custom-fields', [\App\Http\Controllers\VendorStoreController::class, 'addCustomAdFields']);
    Route::get('/ads/{adId}/custom-fields', [\App\Http\Controllers\VendorStoreController::class, 'getCustomAdFields']);
    Route::post('/insurance-policies', [\App\Http\Controllers\VendorStoreController::class, 'createInsurancePolicy']);
    Route::get('/insurance-policies', [\App\Http\Controllers\VendorStoreController::class, 'getUserInsurancePolicies']);
    Route::post('/ads/{adId}/insurance', [\App\Http\Controllers\VendorStoreController::class, 'processAdInsurance']);
    Route::post('/insurance-policies/{policyId}/claim', [\App\Http\Controllers\VendorStoreController::class, 'submitInsuranceClaim']);

    // Social commerce routes
    Route::post('/social-posts', [\App\Http\Controllers\SocialCommerceController::class, 'createPost']);
    Route::get('/social-feed', [\App\Http\Controllers\SocialCommerceController::class, 'getFeed']);
    Route::get('/social-trending', [\App\Http\Controllers\SocialCommerceController::class, 'getTrending']);
    Route::post('/social-posts/{postId}/like', [\App\Http\Controllers\SocialCommerceController::class, 'likePost']);
    Route::post('/social-posts/{postId}/unlike', [\App\Http\Controllers\SocialCommerceController::class, 'unlikePost']);
    Route::post('/social-posts/{postId}/comment', [\App\Http\Controllers\SocialCommerceController::class, 'commentOnPost']);
    Route::post('/social-posts/{postId}/share', [\App\Http\Controllers\SocialCommerceController::class, 'sharePost']);
    Route::post('/social-follow', [\App\Http\Controllers\SocialCommerceController::class, 'followEntity']);
    Route::post('/social-unfollow', [\App\Http\Controllers\SocialCommerceController::class, 'unfollowEntity']);
    Route::get('/social-followers/{userId}', [\App\Http\Controllers\SocialCommerceController::class, 'getFollowers']);
    Route::get('/social-following/{userId}', [\App\Http\Controllers\SocialCommerceController::class, 'getFollowing']);
    Route::get('/social-proof/{productId}/{productType}', [\App\Http\Controllers\SocialCommerceController::class, 'getSocialProof']);
    Route::get('/group-buying', [\App\Http\Controllers\SocialCommerceController::class, 'getGroupBuying']);
    Route::get('/community/{categoryId}', [\App\Http\Controllers\SocialCommerceController::class, 'getCategoryCommunity']);
    Route::get('/reputation/{userId?}', [\App\Http\Controllers\SocialCommerceController::class, 'getUserReputation']);
    Route::get('/influencers', [\App\Http\Controllers\SocialCommerceController::class, 'getInfluencers']);
    Route::get('/live-shopping-events', [\App\Http\Controllers\SocialCommerceController::class, 'getLiveShoppingEvents']);
    Route::get('/ugc-campaigns', [\App\Http\Controllers\SocialCommerceController::class, 'getUserGeneratedContentCampaigns']);

    // Trust & Safety routes
    Route::post('/verifications/biometric/initiate', [\App\Http\Controllers\TrustSafetyController::class, 'initiateBiometricVerification']);
    Route::post('/verifications/biometric/{verificationId}/process', [\App\Http\Controllers\TrustSafetyController::class, 'processBiometricVerification']);
    Route::post('/verifications/video/initiate', [\App\Http\Controllers\TrustSafetyController::class, 'initiateVideoVerification']);
    Route::post('/verifications/video/{verificationId}/process', [\App\Http\Controllers\TrustSafetyController::class, 'processVideoVerification']);
    Route::post('/reports', [\App\Http\Controllers\TrustSafetyController::class, 'createReport']);
    Route::get('/reports', [\App\Http\Controllers\TrustSafetyController::class, 'getUserReports']);
    Route::get('/trust-scores/{userId?}', [\App\Http\Controllers\TrustSafetyController::class, 'getUserTrustScore']);
    Route::get('/verification-status/{userId?}', [\App\Http\Controllers\TrustSafetyController::class, 'getUserVerificationStatus']);
    Route::get('/seller-dashboard/{userId?}', [\App\Http\Controllers\TrustSafetyController::class, 'getSellerPerformanceDashboard']);
    Route::post('/buyer-protection/purchase', [\App\Http\Controllers\TrustSafetyController::class, 'purchaseBuyerProtection']);
    Route::post('/buyer-protection/{protectionId}/claim', [\App\Http\Controllers\TrustSafetyController::class, 'fileProtectionClaim']);
    Route::get('/buyer-protection', [\App\Http\Controllers\TrustSafetyController::class, 'getUserProtections']);
    Route::post('/background-check', [\App\Http\Controllers\TrustSafetyController::class, 'performBackgroundCheck']);

    // Admin Trust & Safety routes
    Route::get('/admin/reports', [\App\Http\Controllers\TrustSafetyController::class, 'getReportsForModeration']);
    Route::put('/admin/reports/{reportId}', [\App\Http\Controllers\TrustSafetyController::class, 'updateReportStatus']);

    // Food vendor routes
    Route::get('/food-vendors', [\App\Http\Controllers\FoodVendorController::class, 'index']);
    Route::get('/food-vendors/{vendorId}', [\App\Http\Controllers\FoodVendorController::class, 'show']);
    Route::get('/food-vendors/search', [\App\Http\Controllers\FoodVendorController::class, 'search']);
    Route::get('/food-vendors/cuisine/{cuisineType}', [\App\Http\Controllers\FoodVendorController::class, 'byCuisine']);
    Route::get('/food-vendors/{vendorId}/menu', [\App\Http\Controllers\FoodVendorController::class, 'getMenu']);
    Route::get('/food-vendors/{vendorId}/menu/category/{category}', [\App\Http\Controllers\FoodVendorController::class, 'getMenuByCategory']);
    Route::get('/food-vendors/{vendorId}/menu/popular', [\App\Http\Controllers\FoodVendorController::class, 'getPopularMenuItems']);
    Route::get('/food-vendors/{vendorId}/menu/new', [\App\Http\Controllers\FoodVendorController::class, 'getNewMenuItems']);
    Route::get('/food-vendors/{vendorId}/menu/dietary/{dietaryOption}', [\App\Http\Controllers\FoodVendorController::class, 'getMenuByDietary']);
    Route::post('/food-orders', [\App\Http\Controllers\FoodVendorController::class, 'placeOrder']);
    Route::get('/food-orders', [\App\Http\Controllers\FoodVendorController::class, 'getOrderHistory']);
    Route::get('/food-orders/{orderNumber}', [\App\Http\Controllers\FoodVendorController::class, 'getOrder']);
    Route::get('/food-vendors/{vendorId}/stats', [\App\Http\Controllers\FoodVendorController::class, 'getVendorStats']);
    Route::put('/food-orders/{orderNumber}/status', [\App\Http\Controllers\FoodVendorController::class, 'updateOrderStatus']);

    // Shopping cart routes
    Route::post('/cart/add', [\App\Http\Controllers\ShoppingCartController::class, 'addToCart']);
    Route::get('/cart', [\App\Http\Controllers\ShoppingCartController::class, 'getCart']);
    Route::put('/cart/{adId}', [\App\Http\Controllers\ShoppingCartController::class, 'updateCartItem']);
    Route::delete('/cart/{adId}', [\App\Http\Controllers\ShoppingCartController::class, 'removeFromCart']);
    Route::delete('/cart', [\App\Http\Controllers\ShoppingCartController::class, 'clearCart']);
    Route::post('/cart/checkout', [\App\Http\Controllers\ShoppingCartController::class, 'createOrder']);
    Route::get('/orders', [\App\Http\Controllers\ShoppingCartController::class, 'getOrderHistory']);
    Route::get('/orders/{orderNumber}', [\App\Http\Controllers\ShoppingCartController::class, 'getOrder']);

    // Advanced listing features
    Route::post('/advanced-listings/upload-360-photos', [\App\Http\Controllers\AdvancedListingController::class, 'upload360Photos']);
    Route::post('/advanced-listings/upload-video', [\App\Http\Controllers\AdvancedListingController::class, 'uploadVideo']);
    Route::post('/advanced-listings/create-vr-tour', [\App\Http\Controllers\AdvancedListingController::class, 'createVRTour']);
    Route::post('/advanced-listings/create-interactive-demo', [\App\Http\Controllers\AdvancedListingController::class, 'createInteractiveDemo']);
    Route::get('/advanced-listings/{adId}/inventory', [\App\Http\Controllers\AdvancedListingController::class, 'getLiveInventory']);
    Route::post('/advanced-listings/{adId}/inventory', [\App\Http\Controllers\AdvancedListingController::class, 'updateInventory']);
    Route::post('/advanced-listings/setup-renew-optimization', [\App\Http\Controllers\AdvancedListingController::class, 'setupAutoRenewOptimization']);
    Route::post('/advanced-listings/run-optimization/{optimizerId}', [\App\Http\Controllers\AdvancedListingController::class, 'runOptimization']);
    Route::post('/advanced-listings/create-ab-test', [\App\Http\Controllers\AdvancedListingController::class, 'createABTest']);
    Route::get('/advanced-listings/ab-test-results/{testId}', [\App\Http\Controllers\AdvancedListingController::class, 'getABTestResults']);
    Route::post('/advanced-listings/book-photography-service', [\App\Http\Controllers\AdvancedListingController::class, 'bookPhotographyService']);
    Route::get('/advanced-listings/features/{adId}', [\App\Http\Controllers\AdvancedListingController::class, 'getAdvancedListingFeatures']);

    // Hero banner management routes (Admin)
    Route::apiResource('hero-banners', \App\Http\Controllers\Api\Admin\HeroBannersController::class);
    Route::patch('/hero-banners/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\HeroBannersController::class, 'toggleStatus']);
    Route::post('/hero-banners/reorder', [\App\Http\Controllers\Api\Admin\HeroBannersController::class, 'reorder']);
    Route::get('/hero-banners/active', [\App\Http\Controllers\Api\Admin\HeroBannersController::class, 'getActiveBanners']);

    // Frontend hero banner routes
    Route::get('/hero-banners/frontend', [\App\Http\Controllers\HeroBannerController::class, 'getActiveBanners']);
    Route::get('/hero-banners/featured', [\App\Http\Controllers\HeroBannerController::class, 'getFeaturedBanners']);
    Route::post('/hero-banners/{id}/click', [\App\Http\Controllers\HeroBannerController::class, 'recordClick']);
    Route::post('/hero-banners/{id}/conversion', [\App\Http\Controllers\HeroBannerController::class, 'recordConversion']);
    Route::get('/hero-banners/{id}', [\App\Http\Controllers\HeroBannerController::class, 'getBanner']);
    Route::get('/hero-section', [\App\Http\Controllers\HeroBannerController::class, 'renderHeroSection']);

    // Advanced listing features
    Route::get('/advanced-listings/active-banners', [\App\Http\Controllers\AdvancedListingController::class, 'getActiveBanners']);
    Route::post('/advanced-listings/360-photos', [\App\Http\Controllers\AdvancedListingController::class, 'upload360Photos']);
    Route::post('/advanced-listings/video', [\App\Http\Controllers\AdvancedListingController::class, 'uploadVideo']);
    Route::post('/advanced-listings/vr-tour', [\App\Http\Controllers\AdvancedListingController::class, 'createVRTour']);
    Route::post('/advanced-listings/interactive-demo', [\App\Http\Controllers\AdvancedListingController::class, 'createInteractiveDemo']);
    Route::get('/advanced-listings/{adId}/inventory', [\App\Http\Controllers\AdvancedListingController::class, 'getLiveInventory']);
    Route::post('/advanced-listings/{adId}/inventory', [\App\Http\Controllers\AdvancedListingController::class, 'updateInventory']);
    Route::post('/advanced-listings/optimization', [\App\Http\Controllers\AdvancedListingController::class, 'setupAutoRenewOptimization']);
    Route::post('/advanced-listings/{optimizerId}/run', [\App\Http\Controllers\AdvancedListingController::class, 'runOptimization']);
    Route::post('/advanced-listings/ab-test', [\App\Http\Controllers\AdvancedListingController::class, 'createABTest']);
    Route::get('/advanced-listings/ab-test/{testId}/results', [\App\Http\Controllers\AdvancedListingController::class, 'getABTestResults']);
    Route::post('/advanced-listings/book-photography', [\App\Http\Controllers\AdvancedListingController::class, 'bookPhotographyService']);
    Route::get('/advanced-listings/features/{adId}', [\App\Http\Controllers\AdvancedListingController::class, 'getAdvancedListingFeatures']);

    // Logistics integration routes
    Route::get('/logistics/partners', [\App\Http\Controllers\LogisticsController::class, 'getLogisticsPartners']);
    Route::post('/logistics/shipping-label', [\App\Http\Controllers\LogisticsController::class, 'generateShippingLabel']);
    Route::post('/logistics/returns', [\App\Http\Controllers\LogisticsController::class, 'processReturnRequest']);
    Route::get('/logistics/returns/dashboard', [\App\Http\Controllers\LogisticsController::class, 'getReturnManagementDashboard']);
    Route::post('/logistics/protection', [\App\Http\Controllers\LogisticsController::class, 'generatePackageInsurance']);
    Route::post('/logistics/warehouse-integration', [\App\Http\Controllers\LogisticsController::class, 'warehouseIntegration']);
    Route::post('/logistics/sync-inventory', [\App\Http\Controllers\LogisticsController::class, 'synchronizeInventory']);
    Route::get('/logistics/user-protections', [\App\Http\Controllers\LogisticsController::class, 'getUserProtections']);
    Route::post('/logistics/protection/{id}/claim', [\App\Http\Controllers\LogisticsController::class, 'fileProtectionClaim']);
    Route::get('/logistics/verification-status/{userId?}', [\App\Http\Controllers\LogisticsController::class, 'getUserVerificationStatus']);
    Route::get('/logistics/trust-score/{userId?}', [\App\Http\Controllers\LogisticsController::class, 'getUserTrustScore']);

    // Admin logistics management routes
    Route::get('/admin/reports/moderation', [\App\Http\Controllers\LogisticsController::class, 'getReportsForModeration']);
    Route::patch('/admin/reports/{id}/status', [\App\Http\Controllers\LogisticsController::class, 'updateReportStatus']);

    // Enhanced insurance features
    Route::post('/insurance/calculate-premium', [\App\Http\Controllers\VendorStoreController::class, 'calculateInsurancePremium']);
    Route::post('/insurance/calculate-emi', [\App\Http\Controllers\VendorStoreController::class, 'calculateInsuranceEMI']);
    Route::post('/insurance/compare', [\App\Http\Controllers\VendorStoreController::class, 'compareInsurancePolicies']);
    Route::get('/insurance/providers', [\App\Http\Controllers\VendorStoreController::class, 'getInsuranceProviders']);
    Route::get('/insurance/dashboard', [\App\Http\Controllers\VendorStoreController::class, 'getUserInsuranceDashboard']);
    Route::get('/insurance/documents', [\App\Http\Controllers\VendorStoreController::class, 'getPolicyDocuments']);
    Route::post('/insurance/term', [\App\Http\Controllers\VendorStoreController::class, 'createTermInsurancePolicy']);

    // Professional seller tools
    Route::get('/seller-tools/inventory-locations', [\App\Http\Controllers\SellerToolsController::class, 'getInventoryLocations']);
    Route::post('/seller-tools/inventory-locations', [\App\Http\Controllers\SellerToolsController::class, 'createInventoryLocation']);
    Route::put('/seller-tools/inventory-locations/{locationId}', [\App\Http\Controllers\SellerToolsController::class, 'updateInventoryLocation']);
    Route::delete('/seller-tools/inventory-locations/{locationId}', [\App\Http\Controllers\SellerToolsController::class, 'deleteInventoryLocation']);
    Route::get('/seller-tools/inventory-items/{locationId}', [\App\Http\Controllers\SellerToolsController::class, 'getInventoryItems']);
    Route::post('/seller-tools/inventory-bulk-update', [\App\Http\Controllers\SellerToolsController::class, 'bulkUpdateInventory']);
    Route::get('/seller-tools/dashboard', [\App\Http\Controllers\SellerToolsController::class, 'getSellerDashboard']);
    Route::get('/seller-tools/seasonal-planning', [\App\Http\Controllers\SellerToolsController::class, 'getSeasonalPlanning']);
    Route::post('/seller-tools/automated-repricing', [\App\Http\Controllers\SellerToolsController::class, 'setupAutomatedRepricing']);
    Route::get('/seller-tools/repricing-recommendations', [\App\Http\Controllers\SellerToolsController::class, 'getRepricingRecommendations']);
    Route::get('/seller-tools/cross-platform-opportunities', [\App\Http\Controllers\SellerToolsController::class, 'getCrossPlatformOpportunities']);
    Route::get('/seller-tools/customer-management', [\App\Http\Controllers\SellerToolsController::class, 'getCustomerManagementData']);
    Route::post('/seller-tools/loyalty-program', [\App\Http\Controllers\SellerToolsController::class, 'setupLoyaltyProgram']);
    Route::get('/seller-tools/customer-segmentation', [\App\Http\Controllers\SellerToolsController::class, 'getCustomerSegmentation']);
    Route::get('/seller-tools/inventory-report', [\App\Http\Controllers\SellerToolsController::class, 'getInventoryPerformanceReport']);
    Route::get('/seller-tools/sales-forecast', [\App\Http\Controllers\SellerToolsController::class, 'getSalesForecast']);

    // Admin category import routes
    Route::post('/categories/import/jiji', [\App\Http\Controllers\Api\Admin\CategoryImportController::class, 'importFromJiji']);
    Route::get('/categories/import/status', [\App\Http\Controllers\Api\Admin\CategoryImportController::class, 'importStatus']);

    // Admin product import routes
    Route::post('/products/import/latest', [\App\Http\Controllers\Api\Admin\CategoryImportController::class, 'importLatestProducts']);
    Route::get('/products/import/settings', [\App\Http\Controllers\Api\Admin\CategoryImportController::class, 'getImportSettings']);
    Route::put('/products/import/settings', [\App\Http\Controllers\Api\Admin\CategoryImportController::class, 'updateImportSettings']);
});

// Advanced Payment Solutions routes
Route::middleware(['auth:sanctum'])->prefix('advanced-payments')->group(function () {
    Route::post('/methods', [App\Http\Controllers\AdvancedPaymentController::class, 'addPaymentMethod']);
    Route::get('/methods', [App\Http\Controllers\AdvancedPaymentController::class, 'getUserPaymentMethods']);
    Route::put('/methods/{id}/default', [App\Http\Controllers\AdvancedPaymentController::class, 'setDefaultPaymentMethod']);

    // Cryptocurrency payments
    Route::post('/cryptocurrency', [App\Http\Controllers\AdvancedPaymentController::class, 'processCryptocurrencyPayment']);
    Route::get('/cryptocurrency/supported', [App\Http\Controllers\AdvancedPaymentController::class, 'getSupportedCryptocurrencies']);

    // Buy-now-pay-later
    Route::post('/bnpl', [App\Http\Controllers\AdvancedPaymentController::class, 'processBuyNowPayLater']);

    // Split payments
    Route::post('/split', [App\Http\Controllers\AdvancedPaymentController::class, 'processSplitPayment']);
    Route::post('/split/{id}/join', [App\Http\Controllers\AdvancedPaymentController::class, 'joinSplitPayment']);

    // Insurance
    Route::post('/insurance', [App\Http\Controllers\AdvancedPaymentController::class, 'processInsurance']);

    // Tax calculation
    Route::post('/tax/calculate', [App\Http\Controllers\AdvancedPaymentController::class, 'calculateTax']);

    // Mobile money
    Route::post('/mobile-money', [App\Http\Controllers\AdvancedPaymentController::class, 'processMobileMoneyPayment']);
});

// Payment Transactions routes
Route::middleware(['auth:sanctum'])->prefix('payment-transactions')->group(function () {
    Route::get('/', [App\Http\Controllers\PaymentTransactionController::class, 'index']);
    Route::post('/', [App\Http\Controllers\PaymentTransactionController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\PaymentTransactionController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\PaymentTransactionController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\PaymentTransactionController::class, 'destroy']);
    Route::get('/ad/{adId}', [App\Http\Controllers\PaymentTransactionController::class, 'forAd']);
    Route::post('/webhook', [App\Http\Controllers\PaymentTransactionController::class, 'webhook'])->withoutMiddleware(['auth:sanctum']);
});

// Two-Factor Authentication routes
Route::middleware(['auth:sanctum'])->prefix('2fa')->group(function () {
    // TOTP (Google Authenticator) routes
    Route::post('/enable', [App\Http\Controllers\TwoFactorAuthController::class, 'enable2FA']);
    Route::post('/confirm', [App\Http\Controllers\TwoFactorAuthController::class, 'confirm2FA']);
    Route::post('/disable', [App\Http\Controllers\TwoFactorAuthController::class, 'disable2FA']);
    Route::post('/verify', [App\Http\Controllers\TwoFactorAuthController::class, 'verify2FA']);

    // SMS 2FA routes
    Route::post('/sms/request', [App\Http\Controllers\TwoFactorAuthController::class, 'requestSmsCode']);

    // Email 2FA routes
    Route::post('/email/request', [App\Http\Controllers\TwoFactorAuthController::class, 'requestEmailCode']);

    // Backup codes routes
    Route::post('/backup/generate', [App\Http\Controllers\TwoFactorAuthController::class, 'generateBackupCodes']);
    Route::post('/backup/verify', [App\Http\Controllers\TwoFactorAuthController::class, 'verifyBackupCode']);

    // Status and info routes
    Route::get('/status', [App\Http\Controllers\TwoFactorAuthController::class, 'getStatus']);
});

// Blockchain Identity Verification routes
Route::middleware(['auth:sanctum'])->prefix('blockchain-verification')->group(function () {
    Route::post('/initiate', [App\Http\Controllers\BlockchainVerificationController::class, 'initiateVerification']);
    Route::get('/status', [App\Http\Controllers\BlockchainVerificationController::class, 'getStatus']);
    Route::get('/verifications', [App\Http\Controllers\BlockchainVerificationController::class, 'getUserVerifications']);
    Route::post('/verify-transaction', [App\Http\Controllers\BlockchainVerificationController::class, 'verifyTransaction']);
    Route::post('/upload-documents', [App\Http\Controllers\BlockchainVerificationController::class, 'uploadDocuments']);
});

// Payment Tokenization routes
Route::middleware(['auth:sanctum'])->prefix('payment-tokenization')->group(function () {
    Route::post('/create', [App\Http\Controllers\PaymentTokenizationController::class, 'createToken']);
    Route::post('/create-single-use', [App\Http\Controllers\PaymentTokenizationController::class, 'createSingleUseToken']);
    Route::post('/retrieve', [App\Http\Controllers\PaymentTokenizationController::class, 'retrievePaymentData']);
    Route::delete('/delete', [App\Http\Controllers\PaymentTokenizationController::class, 'deleteToken']);
    Route::post('/tokenize-card', [App\Http\Controllers\PaymentTokenizationController::class, 'tokenizeCard']);
});

// Device Fingerprinting routes
Route::middleware(['auth:sanctum'])->prefix('device-fingerprint')->group(function () {
    Route::get('/current', [App\Http\Controllers\DeviceFingerprintController::class, 'getCurrentFingerprint']);
    Route::get('/check-suspicious', [App\Http\Controllers\DeviceFingerprintController::class, 'checkSuspiciousDevice']);
    Route::get('/trusted-devices', [App\Http\Controllers\DeviceFingerprintController::class, 'getUserDevices']);
    Route::delete('/trusted-devices/{deviceId}', [App\Http\Controllers\DeviceFingerprintController::class, 'removeDevice']);
    Route::post('/validate', [App\Http\Controllers\DeviceFingerprintController::class, 'validateDeviceToken']);
});

// Biometric Authorization routes
Route::middleware(['auth:sanctum'])->prefix('biometric-auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\BiometricAuthorizationController::class, 'registerTemplate']);
    Route::post('/verify', [App\Http\Controllers\BiometricAuthorizationController::class, 'verifyBiometric']);
    Route::post('/authorize-transaction', [App\Http\Controllers\BiometricAuthorizationController::class, 'authorizeTransaction']);
    Route::get('/templates', [App\Http\Controllers\BiometricAuthorizationController::class, 'getUserTemplates']);
    Route::put('/templates/{templateId}/status', [App\Http\Controllers\BiometricAuthorizationController::class, 'updateTemplateStatus']);
    Route::delete('/templates/{templateId}', [App\Http\Controllers\BiometricAuthorizationController::class, 'deleteTemplate']);
    Route::get('/verification-history', [App\Http\Controllers\BiometricAuthorizationController::class, 'getVerificationHistory']);
});
