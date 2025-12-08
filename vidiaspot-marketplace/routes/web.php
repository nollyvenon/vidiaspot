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
});
