<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\SearchController;

Route::get('/', [LandingController::class, 'index']);
Route::get('/about', [StaticPageController::class, 'about']);
Route::get('/contact', [StaticPageController::class, 'contact']);
Route::get('/help', [StaticPageController::class, 'help']);
Route::get('/safety', [StaticPageController::class, 'safety']);
Route::get('/how-it-works', [StaticPageController::class, 'howItWorks']);
Route::get('/search', [SearchController::class, 'search']);

require __DIR__.'/auth.php';

// User profile and preferences routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/user/feed', [App\Http\Controllers\UserController::class, 'getPersonalizedFeed'])->name('user.feed');
    Route::put('/user/preferences', [App\Http\Controllers\UserController::class, 'updatePreferences'])->name('user.preferences.update');
    Route::post('/user/behavior', [App\Http\Controllers\UserController::class, 'trackBehavior'])->name('user.behavior.track');
    Route::put('/user/notifications', [App\Http\Controllers\UserController::class, 'updateNotificationPreferences'])->name('user.notifications.update');
    Route::get('/settings/personalization', [App\Http\Controllers\UserController::class, 'showPersonalizationSettings'])->name('settings.personalization');
    Route::get('/settings/notifications', [App\Http\Controllers\UserController::class, 'showNotificationSettings'])->name('settings.notifications');
});

// Smart Messaging routes (protected)
Route::middleware(['auth'])->prefix('messaging')->group(function () {
    // Smart replies and auto-responses
    Route::post('/smart-replies', [App\Http\Controllers\SmartMessagingController::class, 'getSmartReplies']);
    Route::post('/translate', [App\Http\Controllers\SmartMessagingController::class, 'translateMessage']);

    // Conversations
    Route::post('/conversations', [App\Http\Controllers\SmartMessagingController::class, 'startConversation']);
    Route::get('/conversations', [App\Http\Controllers\SmartMessagingController::class, 'getUserConversations']);
    Route::get('/conversations/{conversationId}', [App\Http\Controllers\SmartMessagingController::class, 'getConversationHistory']);
    Route::post('/conversations/{conversationId}/messages', [App\Http\Controllers\SmartMessagingController::class, 'sendMessage']);

    // Video calls
    Route::post('/video-call', [App\Http\Controllers\SmartMessagingController::class, 'createVideoCall']);

    // Scheduling
    Route::post('/schedule', [App\Http\Controllers\SmartMessagingController::class, 'scheduleMeeting']);

    // Escrow services
    Route::post('/escrow', [App\Http\Controllers\SmartMessagingController::class, 'createEscrow']);
    Route::post('/escrow/{escrowId}/release', [App\Http\Controllers\SmartMessagingController::class, 'releaseEscrow']);
    Route::get('/escrow/{escrowId}/verify', [App\Http\Controllers\SmartMessagingController::class, 'verifyEscrowOnBlockchain']);
    Route::post('/escrow/{escrowId}/resolve', [App\Http\Controllers\SmartMessagingController::class, 'resolveEscrowDispute']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\SmartMessagingController::class, 'getNotifications']);
    Route::post('/notifications/read', [App\Http\Controllers\SmartMessagingController::class, 'markNotificationsAsRead']);
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
        Route::get('/', [App\Http\Controllers\CryptoP2PController::class, 'index'])->name('crypto-p2p.index');
        Route::get('/create-listing', [App\Http\Controllers\CryptoP2PController::class, 'createListing'])->name('crypto-p2p.create-listing');
        Route::post('/listings', [App\Http\Controllers\CryptoP2PController::class, 'storeListing'])->name('crypto-p2p.store-listing');

        Route::get('/listings/{id}', [App\Http\Controllers\CryptoP2PController::class, 'showListing'])->name('crypto-p2p.show-listing');
        Route::get('/listings/{id}/initiate-trade', [App\Http\Controllers\CryptoP2PController::class, 'initiateTrade'])->name('crypto-p2p.initiate-trade');

        Route::post('/trades/{listingId}', [App\Http\Controllers\CryptoP2PController::class, 'storeTrade'])->name('crypto-p2p.store-trade');
        Route::get('/trades/{id}', [App\Http\Controllers\CryptoP2PController::class, 'showTrade'])->name('crypto-p2p.show-trade');
        Route::post('/trades/{id}/confirm-payment', [App\Http\Controllers\CryptoP2PController::class, 'confirmPayment'])->name('crypto-p2p.confirm-payment');
        Route::post('/trades/{id}/release-crypto', [App\Http\Controllers\CryptoP2PController::class, 'releaseCrypto'])->name('crypto-p2p.release-crypto');

        Route::get('/my-listings', [App\Http\Controllers\CryptoP2PController::class, 'getUserListings'])->name('crypto-p2p.my-listings');
        Route::get('/my-trades', [App\Http\Controllers\CryptoP2PController::class, 'getUserTrades'])->name('crypto-p2p.my-trades');
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
    Route::get('/setup', [App\Http\Controllers\VendorStoreController::class, 'create'])->name('vendor.store.setup');
    Route::post('/setup', [App\Http\Controllers\VendorStoreController::class, 'store'])->name('vendor.store.store');
    Route::get('/edit', [App\Http\Controllers\VendorStoreController::class, 'edit'])->name('vendor.store.edit');
    Route::put('/{id}', [App\Http\Controllers\VendorStoreController::class, 'update'])->name('vendor.store.update');
    Route::get('/{slug}', [App\Http\Controllers\VendorStoreController::class, 'show'])->name('vendor.store.show');
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
    Route::get('/settings', [App\Http\Controllers\AccessibilityController::class, 'getSettings'])->name('accessibility.settings');
    Route::put('/settings', [App\Http\Controllers\AccessibilityController::class, 'updateSettings'])->name('accessibility.settings.update');
    Route::post('/aria-attributes', [App\Http\Controllers\AccessibilityController::class, 'getAriaAttributes'])->name('accessibility.aria');
    Route::post('/element-attributes', [App\Http\Controllers\AccessibilityController::class, 'getAccessibilityAttributes'])->name('accessibility.element');
    Route::post('/skip-link', [App\Http\Controllers\AccessibilityController::class, 'getSkipLink'])->name('accessibility.skip-link');
    Route::post('/screen-reader-text', [App\Http\Controllers\AccessibilityController::class, 'getScreenReaderOnly'])->name('accessibility.screen-reader');
    Route::post('/accessible-table', [App\Http\Controllers\AccessibilityController::class, 'createAccessibleTable'])->name('accessibility.table');
});

// Voice Navigation routes
Route::middleware(['auth'])->prefix('voice-navigation')->group(function () {
    Route::post('/command', [App\Http\Controllers\VoiceNavigationController::class, 'processCommand'])->name('voice.command');
    Route::post('/transcribe', [App\Http\Controllers\VoiceNavigationController::class, 'transcribeSpeech'])->name('voice.transcribe');
    Route::get('/commands', [App\Http\Controllers\VoiceNavigationController::class, 'getCommands'])->name('voice.commands');
    Route::post('/start-session', [App\Http\Controllers\VoiceNavigationController::class, 'startSession'])->name('voice.start');
    Route::post('/end-session', [App\Http\Controllers\VoiceNavigationController::class, 'endSession'])->name('voice.end');
    Route::get('/status', [App\Http\Controllers\VoiceNavigationController::class, 'getStatus'])->name('voice.status');
});

// Sign Language Video routes
Route::prefix('sign-language-videos')->group(function () {
    Route::get('/', [App\Http\Controllers\SignLanguageVideoController::class, 'index'])->name('sign-language-videos.index');
    Route::post('/upload', [App\Http\Controllers\SignLanguageVideoController::class, 'upload'])->name('sign-language-videos.upload');
    Route::get('/search', [App\Http\Controllers\SignLanguageVideoController::class, 'search'])->name('sign-language-videos.search');
    Route::get('/categories', [App\Http\Controllers\SignLanguageVideoController::class, 'getCategories'])->name('sign-language-videos.categories');
    Route::get('/trending', [App\Http\Controllers\SignLanguageVideoController::class, 'getTrending'])->name('sign-language-videos.trending');
    Route::get('/recommended', [App\Http\Controllers\SignLanguageVideoController::class, 'getRecommended'])->name('sign-language-videos.recommended');
    Route::get('/history', [App\Http\Controllers\SignLanguageVideoController::class, 'getViewingHistory'])->name('sign-language-videos.history');
    Route::get('/{videoId}', [App\Http\Controllers\SignLanguageVideoController::class, 'show'])->name('sign-language-videos.show');
    Route::get('/category/{category}', [App\Http\Controllers\SignLanguageVideoController::class, 'getByCategory'])->name('sign-language-videos.category');
});

// Cognitive Accessibility routes
Route::middleware(['auth'])->prefix('cognitive-accessibility')->group(function () {
    Route::get('/settings', [App\Http\Controllers\CognitiveAccessibilityController::class, 'getSettings'])->name('cognitive.settings');
    Route::put('/settings', [App\Http\Controllers\CognitiveAccessibilityController::class, 'updateSettings'])->name('cognitive.settings.update');
    Route::post('/simplify-text', [App\Http\Controllers\CognitiveAccessibilityController::class, 'simplifyText'])->name('cognitive.simplify');
    Route::post('/alternative-formats', [App\Http\Controllers\CognitiveAccessibilityController::class, 'getAlternativeFormats'])->name('cognitive.alternatives');
    Route::post('/simplify-interface', [App\Http\Controllers\CognitiveAccessibilityController::class, 'createSimplifiedInterface'])->name('cognitive.simplify.interface');
    Route::post('/simplify-page', [App\Http\Controllers\CognitiveAccessibilityController::class, 'getSimplifiedPage'])->name('cognitive.simplify.page');
    Route::get('/guidelines', [App\Http\Controllers\CognitiveAccessibilityController::class, 'getCognitiveGuidelines'])->name('cognitive.guidelines');
    Route::get('/status', [App\Http\Controllers\CognitiveAccessibilityController::class, 'getStatus'])->name('cognitive.status');
});

// Offline Mode routes
Route::middleware(['auth'])->prefix('offline-mode')->group(function () {
    Route::post('/prepare', [App\Http\Controllers\OfflineModeController::class, 'prepareForOffline'])->name('offline.prepare');
    Route::get('/content', [App\Http\Controllers\OfflineModeController::class, 'getOfflineContent'])->name('offline.content');
    Route::post('/sync', [App\Http\Controllers\OfflineModeController::class, 'syncOfflineChanges'])->name('offline.sync');
    Route::get('/available', [App\Http\Controllers\OfflineModeController::class, 'getAvailableContent'])->name('offline.available');
    Route::get('/status', [App\Http\Controllers\OfflineModeController::class, 'getStatus'])->name('offline.status');
    Route::get('/check-availability', [App\Http\Controllers\OfflineModeController::class, 'checkAvailability'])->name('offline.check');
    Route::post('/cleanup', [App\Http\Controllers\OfflineModeController::class, 'cleanup'])->name('offline.cleanup');
    Route::delete('/package/{packageId}', [App\Http\Controllers\OfflineModeController::class, 'removePackage'])->name('offline.remove');
    Route::get('/package/{packageId}/download', [App\Http\Controllers\OfflineModeController::class, 'downloadPackage'])->name('offline.download');
});

// Low Bandwidth Optimization routes
Route::middleware(['auth'])->prefix('low-bandwidth')->group(function () {
    Route::get('/status', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'getStatus'])->name('lowbandwidth.status');
    Route::put('/toggle', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'toggleMode'])->name('lowbandwidth.toggle');
    Route::post('/optimize-image', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'optimizeImage'])->name('lowbandwidth.optimize.image');
    Route::post('/optimize-text', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'optimizeTextContent'])->name('lowbandwidth.optimize.text');
    Route::post('/optimize-response', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'optimizeResponse'])->name('lowbandwidth.optimize.response');
    Route::post('/generate-low-bandwidth-page', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'generateLowBandwidthPage'])->name('lowbandwidth.generate.page');
    Route::post('/recommendations', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'getRecommendations'])->name('lowbandwidth.recommendations');
    Route::get('/should-activate', [App\Http\Controllers\LowBandwidthOptimizationController::class, 'shouldActivate'])->name('lowbandwidth.should.activate');
});

// Multiple Input Methods routes
Route::middleware(['auth'])->prefix('input-methods')->group(function () {
    Route::get('/preferences', [App\Http\Controllers\MultipleInputMethodsController::class, 'getPreferences'])->name('input-methods.preferences');
    Route::put('/preferences', [App\Http\Controllers\MultipleInputMethodsController::class, 'updatePreferences'])->name('input-methods.preferences.update');
    Route::post('/voice', [App\Http\Controllers\MultipleInputMethodsController::class, 'processVoiceInput'])->name('input-methods.voice');
    Route::post('/gesture', [App\Http\Controllers\MultipleInputMethodsController::class, 'processGestureInput'])->name('input-methods.gesture');
    Route::post('/touch', [App\Http\Controllers\MultipleInputMethodsController::class, 'processTouchInput'])->name('input-methods.touch');
    Route::post('/adapt-interface', [App\Http\Controllers\MultipleInputMethodsController::class, 'adaptInterface'])->name('input-methods.adapt');
    Route::get('/analytics', [App\Http\Controllers\MultipleInputMethodsController::class, 'getAnalytics'])->name('input-methods.analytics');
    Route::get('/supported', [App\Http\Controllers\MultipleInputMethodsController::class, 'getSupportedMethods'])->name('input-methods.supported');
    Route::post('/calibrate-gestures', [App\Http\Controllers\MultipleInputMethodsController::class, 'calibrateGestures'])->name('input-methods.calibrate');
});

// Accessibility settings page
Route::get('/accessibility', function () {
    return view('accessibility.settings');
})->name('accessibility.page');
