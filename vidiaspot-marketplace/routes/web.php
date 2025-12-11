<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\General\LandingController;
use App\Http\Controllers\General\StaticPageController;
use App\Http\Controllers\AdvancedTech\SearchController;

Route::get('/', [LandingController::class, 'index']);
Route::get('/about', [StaticPageController::class, 'about']);
Route::get('/contact', [StaticPageController::class, 'contact']);
Route::get('/help', [StaticPageController::class, 'help']);
Route::get('/safety', [StaticPageController::class, 'safety']);
Route::get('/how-it-works', [StaticPageController::class, 'howItWorks']);
Route::get('/search', [SearchController::class, 'search']);

// Farm-specific routes
Route::get('/farm-marketplace', function() {
    return view('landing.farm_marketplace');
})->name('farm.marketplace');

Route::get('/farm-buyer', function() {
    return view('landing.farm_buyer_landing');
})->name('farm.buyer.landing');

Route::get('/farm-seller', function() {
    return view('landing.farm_seller_landing');
})->name('farm.seller.landing');

// Farm products routes
Route::get('/farm-products', [App\Http\Controllers\Web\FarmProductController::class, 'index'])->name('farm.products.index');
Route::get('/farm-products/{id}', [App\Http\Controllers\Web\FarmProductController::class, 'show'])->where('id', '[0-9]+')->name('farm.products.show');
Route::get('/farmers/{id}', [App\Http\Controllers\Web\FarmProductController::class, 'sellerProfile'])->name('farm.seller.profile');

// Additional farm routes
Route::get('/farm-categories', function() {
    return view('landing.farm_categories');
})->name('farm.categories');

require __DIR__.'/auth.php';

// User profile and preferences routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/user/feed', [App\Http\Controllers\Authentication\UserController::class, 'getPersonalizedFeed'])->name('user.feed');
    Route::put('/user/preferences', [App\Http\Controllers\Authentication\UserController::class, 'updatePreferences'])->name('user.preferences.update');
    Route::post('/user/behavior', [App\Http\Controllers\Authentication\UserController::class, 'trackBehavior'])->name('user.behavior.track');
    Route::put('/user/notifications', [App\Http\Controllers\Authentication\UserController::class, 'updateNotificationPreferences'])->name('user.notifications.update');
    Route::get('/settings/personalization', [App\Http\Controllers\Authentication\UserController::class, 'showPersonalizationSettings'])->name('settings.personalization');
    Route::get('/settings/notifications', [App\Http\Controllers\Authentication\UserController::class, 'showNotificationSettings'])->name('settings.notifications');
});

// Smart Messaging routes (protected)
Route::middleware(['auth'])->prefix('messaging')->group(function () {
    // Smart replies and auto-responses
    Route::post('/smart-replies', [App\Http\Controllers\Communication\SmartMessagingController::class, 'getSmartReplies']);
    Route::post('/translate', [App\Http\Controllers\Communication\SmartMessagingController::class, 'translateMessage']);

    // Conversations
    Route::post('/conversations', [App\Http\Controllers\Communication\SmartMessagingController::class, 'startConversation']);
    Route::get('/conversations', [App\Http\Controllers\Communication\SmartMessagingController::class, 'getUserConversations']);
    Route::get('/conversations/{conversationId}', [App\Http\Controllers\Communication\SmartMessagingController::class, 'getConversationHistory']);
    Route::post('/conversations/{conversationId}/messages', [App\Http\Controllers\Communication\SmartMessagingController::class, 'sendMessage']);

    // Video calls
    Route::post('/video-call', [App\Http\Controllers\Communication\SmartMessagingController::class, 'createVideoCall']);

    // Scheduling
    Route::post('/schedule', [App\Http\Controllers\Communication\SmartMessagingController::class, 'scheduleMeeting']);

    // Escrow services
    Route::post('/escrow', [App\Http\Controllers\Communication\SmartMessagingController::class, 'createEscrow']);
    Route::post('/escrow/{escrowId}/release', [App\Http\Controllers\Communication\SmartMessagingController::class, 'releaseEscrow']);
    Route::get('/escrow/{escrowId}/verify', [App\Http\Controllers\Communication\SmartMessagingController::class, 'verifyEscrowOnBlockchain']);
    Route::post('/escrow/{escrowId}/resolve', [App\Http\Controllers\Communication\SmartMessagingController::class, 'resolveEscrowDispute']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Communication\SmartMessagingController::class, 'getNotifications']);
    Route::post('/notifications/read', [App\Http\Controllers\Communication\SmartMessagingController::class, 'markNotificationsAsRead']);
});

// Advanced Payment routes (protected)
Route::middleware(['auth'])->prefix('payments')->group(function () {
    Route::get('/methods', function() {
        return view('payments.methods');
    })->name('payments.methods');
    Route::get('/cryptocurrency', function() {
        return view('payments.cryptocurrency');
    })->name('payments.cryptocurrency');

    // P2P Crypto Marketplace routes
    Route::middleware(['auth'])->prefix('crypto-p2p')->group(function () {
        Route::get('/', [App\Http\Controllers\Payments\CryptoP2PController::class, 'index'])->name('crypto-p2p.index');
        Route::get('/create-listing', [App\Http\Controllers\Payments\CryptoP2PController::class, 'createListing'])->name('crypto-p2p.create-listing');
        Route::post('/listings', [App\Http\Controllers\Payments\CryptoP2PController::class, 'storeListing'])->name('crypto-p2p.store-listing');

        Route::get('/listings/{id}', [App\Http\Controllers\Payments\CryptoP2PController::class, 'showListing'])->name('crypto-p2p.show-listing');
        Route::get('/listings/{id}/initiate-trade', [App\Http\Controllers\Payments\CryptoP2PController::class, 'initiateTrade'])->name('crypto-p2p.initiate-trade');

        Route::post('/trades/{listingId}', [App\Http\Controllers\Payments\CryptoP2PController::class, 'storeTrade'])->name('crypto-p2p.store-trade');
        Route::get('/trades/{id}', [App\Http\Controllers\Payments\CryptoP2PController::class, 'showTrade'])->name('crypto-p2p.show-trade');
        Route::post('/trades/{id}/confirm-payment', [App\Http\Controllers\Payments\CryptoP2PController::class, 'confirmPayment'])->name('crypto-p2p.confirm-payment');
        Route::post('/trades/{id}/release-crypto', [App\Http\Controllers\Payments\CryptoP2PController::class, 'releaseCrypto'])->name('crypto-p2p.release-crypto');

        Route::get('/my-listings', [App\Http\Controllers\Payments\CryptoP2PController::class, 'getUserListings'])->name('crypto-p2p.my-listings');
        Route::get('/my-trades', [App\Http\Controllers\Payments\CryptoP2PController::class, 'getUserTrades'])->name('crypto-p2p.my-trades');
    });

    Route::get('/bnpl', function() {
        return view('payments.bnpl');
    })->name('payments.bnpl');
    Route::get('/split', function() {
        return view('payments.split');
    })->name('payments.split');
    Route::get('/insurance', function() {
        return view('payments.insurance');
    })->name('payments.insurance');
});

// Admin routes (protected)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('how-it-works', \App\Http\Controllers\Admin\HowItWorksController::class)->names([
        'index' => 'admin.how-it-works.index',
        'create' => 'admin.how-it-works.create',
        'store' => 'admin.how-it-works.store',
        'edit' => 'admin.how-it-works.edit',
        'update' => 'admin.how-it-works.update',
        'destroy' => 'admin.how-it-works.destroy',
    ]);

    Route::resource('payment-settings', \App\Http\Controllers\Admin\PaymentSettingController::class)->names([
        'index' => 'admin.payment-settings.index',
        'create' => 'admin.payment-settings.create',
        'store' => 'admin.payment-settings.store',
        'show' => 'admin.payment-settings.show',
        'edit' => 'admin.payment-settings.edit',
        'update' => 'admin.payment-settings.update',
        'destroy' => 'admin.payment-settings.destroy',
    ]);

    Route::patch('/payment-settings/{id}/toggle-status', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'toggleStatus'])
        ->name('admin.payment-settings.toggle-status');
});

// API documentation route
Route::get('/api-docs', function () {
    return view('api.documentation');
})->name('api.documentation');

// Vendor store routes (protected)
Route::middleware(['auth'])->prefix('store')->group(function () {
    Route::get('/setup', [App\Http\Controllers\ECommerce\VendorStoreController::class, 'create'])->name('vendor.store.setup');
    Route::post('/setup', [App\Http\Controllers\ECommerce\VendorStoreController::class, 'store'])->name('vendor.store.store');
    Route::get('/edit', [App\Http\Controllers\ECommerce\VendorStoreController::class, 'edit'])->name('vendor.store.edit');
    Route::put('/{id}', [App\Http\Controllers\ECommerce\VendorStoreController::class, 'update'])->name('vendor.store.update');
    Route::get('/{slug}', [App\Http\Controllers\ECommerce\VendorStoreController::class, 'show'])->name('vendor.store.show');
});

// User personalization and settings routes (protected)
Route::middleware(['auth'])->prefix('settings')->group(function () {
    Route::get('/personalization', function () {
        return view('settings.personalization');
    })->name('settings.personalization');

    Route::get('/notifications', function () {
        return view('settings.notifications');
    })->name('settings.notifications');

    Route::get('/payment-methods', function () {
        return view('settings.payment-methods');
    })->name('settings.payment-methods');
});

// Accessibility routes
Route::middleware(['auth'])->prefix('accessibility')->group(function () {
    Route::get('/settings', [App\Http\Controllers\Accessibility\AccessibilityController::class, 'getSettings'])->name('accessibility.settings');
    Route::put('/settings', [App\Http\Controllers\Accessibility\AccessibilityController::class, 'updateSettings'])->name('accessibility.settings.update');
    Route::post('/aria-attributes', [App\Http\Controllers\Accessibility\AccessibilityController::class, 'getAriaAttributes'])->name('accessibility.aria');
    Route::post('/element-attributes', [App\Http\Controllers\Accessibility\AccessibilityController::class, 'getAccessibilityAttributes'])->name('accessibility.element');
    Route::post('/skip-link', [App\Http\Controllers\Accessibility\AccessibilityController::class, 'getSkipLink'])->name('accessibility.skip-link');
    Route::post('/screen-reader-text', [App\Http\Controllers\Accessibility\AccessibilityController::class, 'getScreenReaderOnly'])->name('accessibility.screen-reader');
    Route::post('/accessible-table', [App\Http\Controllers\Accessibility\AccessibilityController::class, 'createAccessibleTable'])->name('accessibility.table');
});

// Voice Navigation routes
Route::middleware(['auth'])->prefix('voice-navigation')->group(function () {
    Route::post('/command', [App\Http\Controllers\Accessibility\VoiceNavigationController::class, 'processCommand'])->name('voice.command');
    Route::post('/transcribe', [App\Http\Controllers\Accessibility\VoiceNavigationController::class, 'transcribeSpeech'])->name('voice.transcribe');
    Route::get('/commands', [App\Http\Controllers\Accessibility\VoiceNavigationController::class, 'getCommands'])->name('voice.commands');
    Route::post('/start-session', [App\Http\Controllers\Accessibility\VoiceNavigationController::class, 'startSession'])->name('voice.start');
    Route::post('/end-session', [App\Http\Controllers\Accessibility\VoiceNavigationController::class, 'endSession'])->name('voice.end');
    Route::get('/status', [App\Http\Controllers\Accessibility\VoiceNavigationController::class, 'getStatus'])->name('voice.status');
});

// Sign Language Video routes
Route::prefix('sign-language-videos')->group(function () {
    Route::get('/', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'index'])->name('sign-language-videos.index');
    Route::post('/upload', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'upload'])->name('sign-language-videos.upload');
    Route::get('/search', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'search'])->name('sign-language-videos.search');
    Route::get('/categories', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'getCategories'])->name('sign-language-videos.categories');
    Route::get('/trending', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'getTrending'])->name('sign-language-videos.trending');
    Route::get('/recommended', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'getRecommended'])->name('sign-language-videos.recommended');
    Route::get('/history', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'getViewingHistory'])->name('sign-language-videos.history');
    Route::get('/{videoId}', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'show'])->name('sign-language-videos.show');
    Route::get('/category/{category}', [App\Http\Controllers\Accessibility\SignLanguageVideoController::class, 'getByCategory'])->name('sign-language-videos.category');
});

// Cognitive Accessibility routes
Route::middleware(['auth'])->prefix('cognitive-accessibility')->group(function () {
    Route::get('/settings', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'getSettings'])->name('cognitive.settings');
    Route::put('/settings', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'updateSettings'])->name('cognitive.settings.update');
    Route::post('/simplify-text', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'simplifyText'])->name('cognitive.simplify');
    Route::post('/alternative-formats', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'getAlternativeFormats'])->name('cognitive.alternatives');
    Route::post('/simplify-interface', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'createSimplifiedInterface'])->name('cognitive.simplify.interface');
    Route::post('/simplify-page', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'getSimplifiedPage'])->name('cognitive.simplify.page');
    Route::get('/guidelines', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'getCognitiveGuidelines'])->name('cognitive.guidelines');
    Route::get('/status', [App\Http\Controllers\Accessibility\CognitiveAccessibilityController::class, 'getStatus'])->name('cognitive.status');
});

// Offline Mode routes
Route::middleware(['auth'])->prefix('offline-mode')->group(function () {
    Route::post('/prepare', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'prepareForOffline'])->name('offline.prepare');
    Route::get('/content', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'getOfflineContent'])->name('offline.content');
    Route::post('/sync', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'syncOfflineChanges'])->name('offline.sync');
    Route::get('/available', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'getAvailableContent'])->name('offline.available');
    Route::get('/status', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'getStatus'])->name('offline.status');
    Route::get('/check-availability', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'checkAvailability'])->name('offline.check');
    Route::post('/cleanup', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'cleanup'])->name('offline.cleanup');
    Route::delete('/package/{packageId}', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'removePackage'])->name('offline.remove');
    Route::get('/package/{packageId}/download', [App\Http\Controllers\AdvancedTech\OfflineModeController::class, 'downloadPackage'])->name('offline.download');
});

// Low Bandwidth Optimization routes
Route::middleware(['auth'])->prefix('low-bandwidth')->group(function () {
    Route::get('/status', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'getStatus'])->name('lowbandwidth.status');
    Route::put('/toggle', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'toggleMode'])->name('lowbandwidth.toggle');
    Route::post('/optimize-image', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'optimizeImage'])->name('lowbandwidth.optimize.image');
    Route::post('/optimize-text', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'optimizeTextContent'])->name('lowbandwidth.optimize.text');
    Route::post('/optimize-response', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'optimizeResponse'])->name('lowbandwidth.optimize.response');
    Route::post('/generate-low-bandwidth-page', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'generateLowBandwidthPage'])->name('lowbandwidth.generate.page');
    Route::post('/recommendations', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'getRecommendations'])->name('lowbandwidth.recommendations');
    Route::get('/should-activate', [App\Http\Controllers\AdvancedTech\LowBandwidthOptimizationController::class, 'shouldActivate'])->name('lowbandwidth.should.activate');
});

// Multiple Input Methods routes
Route::middleware(['auth'])->prefix('input-methods')->group(function () {
    Route::get('/preferences', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'getPreferences'])->name('input-methods.preferences');
    Route::put('/preferences', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'updatePreferences'])->name('input-methods.preferences.update');
    Route::post('/voice', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'processVoiceInput'])->name('input-methods.voice');
    Route::post('/gesture', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'processGestureInput'])->name('input-methods.gesture');
    Route::post('/touch', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'processTouchInput'])->name('input-methods.touch');
    Route::post('/adapt-interface', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'adaptInterface'])->name('input-methods.adapt');
    Route::get('/analytics', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'getAnalytics'])->name('input-methods.analytics');
    Route::get('/supported', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'getSupportedMethods'])->name('input-methods.supported');
    Route::post('/calibrate-gestures', [App\Http\Controllers\Accessibility\MultipleInputMethodsController::class, 'calibrateGestures'])->name('input-methods.calibrate');
});

// Accessibility settings page
Route::get('/accessibility', function () {
    return view('accessibility.settings');
})->name('accessibility.page');

// Reports routes
Route::middleware(['auth:sanctum'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'index'])->name('index');
    Route::get('/generate/{type}', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generate'])->name('generate');
    Route::get('/list/{type}', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'list'])->name('list');
    Route::get('/view/{type}/{id}', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'show'])->name('show');
    Route::get('/live-dashboard', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'liveDashboard'])->name('live-dashboard');

    // Food Reporting Routes
    Route::get('/generate/food-sales-revenue', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateFoodSalesRevenueReport']);
    Route::get('/generate/food-operational-efficiency', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateFoodOperationalEfficiencyReport']);
    Route::get('/generate/food-customer-experience', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateFoodCustomerExperienceReport']);
    Route::get('/generate/food-financial', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateFoodFinancialReport']);

    // Classified Reporting Routes
    Route::get('/generate/classified-user-activity', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateClassifiedUserActivityReport']);
    Route::get('/generate/classified-revenue', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateClassifiedRevenueReport']);
    Route::get('/generate/classified-content-quality', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateClassifiedContentQualityReport']);
    Route::get('/generate/classified-market-intelligence', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateClassifiedMarketIntelligenceReport']);

    // E-commerce Reporting Routes
    Route::get('/generate/ecommerce-sales-performance', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateEcommerceSalesPerformanceReport']);
    Route::get('/generate/ecommerce-inventory', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateEcommerceInventoryReport']);
    Route::get('/generate/ecommerce-marketing-customer', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateEcommerceMarketingCustomerReport']);
    Route::get('/generate/ecommerce-financial-operational', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateEcommerceFinancialOperationalReport']);

    // Cross-Platform Reporting Routes
    Route::get('/generate/unified-financial-dashboard', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateUnifiedFinancialDashboardReport']);
    Route::get('/generate/cross-platform-customer-journey', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateCrossPlatformCustomerJourneyReport']);
    Route::get('/generate/cross-platform-operational-efficiency', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateCrossPlatformOperationalEfficiencyReport']);
    Route::get('/generate/cross-platform-risk-management', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateCrossPlatformRiskManagementReport']);

    // Logistics Reporting Routes
    Route::get('/generate/logistics-shipment', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateLogisticsShipmentReport']);
    Route::get('/generate/logistics-delivery', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateLogisticsDeliveryReport']);
    Route::get('/generate/logistics-warehouse', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateLogisticsWarehouseReport']);
    Route::get('/generate/logistics-courier-performance', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateLogisticsCourierPerformanceReport']);
    Route::get('/generate/logistics-return', [App\Http\Controllers\AdvancedTech\ReportsController::class, 'generateLogisticsReturnReport']);
});
