<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BlogController;

/*
|--------------------------------------------------------------------------
| Blog Routes
|--------------------------------------------------------------------------
|
| Web routes for blog functionality
|
*/

// Public blog routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
    Route::get('/category/{category}', [BlogController::class, 'showByCategory'])->name('category');
    Route::get('/featured', [BlogController::class, 'featured'])->name('featured');
    Route::get('/trending', [BlogController::class, 'trending'])->name('trending');
});