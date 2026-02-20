<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Ai\GenerateTextController;

Route::middleware(['auth'])
    ->name('dashboard.ai')
    ->prefix('dashboard/ai')
    ->group(function() {
        Route::post('/generate/text', GenerateTextController::class)
            ->name('.generate.text');
    });