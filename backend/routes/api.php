<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Traditional authentication
    Route::post('login', [AuthController::class, 'login'])->middleware(['throttle:login', 'turnstile']);
    Route::post('register', [AuthController::class, 'register'])->middleware('turnstile');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth.jwt');

    // Social authentication
    Route::prefix('social')->group(function () {
        Route::get('providers', [SocialAuthController::class, 'providers']);
        Route::get('{provider}', [SocialAuthController::class, 'redirect'])
            ->where('provider', 'google|facebook|twitter')
            ->middleware('throttle:social-auth');
        Route::get('{provider}/callback', [SocialAuthController::class, 'callback'])
            ->where('provider', 'google|facebook|twitter')
            ->middleware('throttle:social-auth');
    });
});