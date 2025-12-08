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
