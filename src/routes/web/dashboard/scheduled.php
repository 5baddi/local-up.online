<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media\NewMediaController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media\ScheduledMediaController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\ScheduledPostsController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\SaveScheduledPostController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\EditScheduledPostsController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\DeleteScheduledPostController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Media\DeleteScheduledMediaController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\DeleteScheduledPostMediaController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\UploadScheduledPostMediaController;

Route::middleware(['auth'])
    ->name('dashboard.scheduled')
    ->prefix('dashboard/scheduled')
    ->group(function() {
        Route::name('.posts')
            ->prefix('/posts')
            ->group(function() {
                Route::get('/', ScheduledPostsController::class);
                Route::post('/upload/{id}', UploadScheduledPostMediaController::class)->name('.upload.media');
                Route::delete('/delete/{id}', DeleteScheduledPostMediaController::class)->name('.delete.media');

                Route::get('/{type}/{id?}', EditScheduledPostsController::class)->name('.edit');
                Route::post('/{type}', SaveScheduledPostController::class)->name('.save');

                Route::delete('/{id}', DeleteScheduledPostController::class)
                    ->whereUuid('id')
                    ->name('.delete');
            });
            
        Route::name('.media')
            ->prefix('/media')
            ->group(function() {
                Route::get('/', ScheduledMediaController::class);
                Route::get('/{id}', NewMediaController::class)->name('.edit');
                Route::delete('/{id}', DeleteScheduledMediaController::class)->name('.delete');
            });
    });