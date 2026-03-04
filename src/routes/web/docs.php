<?php

/**
 * ClnkGO
 *
 * @copyright Copyright (c) 2022, BADDI Services. (https://baddi.info)
 */

use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    return redirect('/api-docs/index.html');
})->name('api.docs');

Route::get('/api-docs/openapi.json', function () {
    $path = public_path('api-docs/openapi.json');

    return response()->file($path, [
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Origin' => '*',
    ]);
})->name('api.docs.json');
