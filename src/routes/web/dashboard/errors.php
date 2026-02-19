<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Errors\UnauthenticatedGmbAccessController;

Route::middleware(['auth'])
    ->name('dashboard.errors')
    ->prefix('dashboard/error')
    ->group(function() {
        Route::get('/unauthenticated/gmb', UnauthenticatedGmbAccessController::class)
            ->name('.unauthenticated_gmb_access');
    });