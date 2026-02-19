<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Reviews\IndexController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Reviews\ViewReviewController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Reviews\SaveReviewController;

Route::middleware(['auth'])
    ->name('dashboard.reviews')
    ->prefix('dashboard/reviews')
    ->group(function() {
        Route::get('/', IndexController::class);
        Route::get('/{id}', ViewReviewController::class)->name('.view');
        Route::post('/{id}', SaveReviewController::class)->name('.reply.update');
    });