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

// Accessibility settings page
Route::get('/accessibility', function () {
    return view('accessibility.settings');
})->name('accessibility.page');
