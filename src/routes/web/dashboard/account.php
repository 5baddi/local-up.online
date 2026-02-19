<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account\AccountController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account\UpdateAccountController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account\SetAccountMainLocationController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account\GoogleMyBusinessCallbackController;
use BADDIServices\ClnkGO\Http\Controllers\Dashboard\Account\GoogleMyBusinessDisconnectController;

Route::middleware(['auth'])
    ->name('dashboard.account')
    ->prefix('dashboard/account')
    ->group(function() {
        Route::get('/', AccountController::class);
        Route::post('/', UpdateAccountController::class)->name('.save');
        Route::get('/gmb/callback', GoogleMyBusinessCallbackController::class)->name('.gmb.callback');
        Route::get('/gmb/disconnect', GoogleMyBusinessDisconnectController::class)->name('.gmb.disconnect');
        Route::get('/locations/main', SetAccountMainLocationController::class)->name('.locations.main');
    });