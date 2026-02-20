<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media\IndexController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media\NewMediaController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media\DeleteMediaController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media\UploadMediaController;

Route::middleware(['auth'])
    ->name('dashboard.media')
    ->prefix('dashboard/media')
    ->group(function() {
        Route::get('/', IndexController::class);
        Route::delete('/{id}', DeleteMediaController::class)->name('.delete');

        Route::get('/new', NewMediaController::class)->name('.new');
        Route::post('/new', UploadMediaController::class)->name('.upload');
    });