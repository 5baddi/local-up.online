<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\ViewPostController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Posts\DeletePostController;

Route::middleware(['auth'])
    ->name('dashboard.posts')
    ->prefix('dashboard/posts')
    ->group(function() {
        Route::get(
                '/{accountId}/{locationId?}/{postId?}',
                ViewPostController::class
            )
            ->name('.view');

        Route::delete('/{id}', DeletePostController::class)->name('.delete');
    });