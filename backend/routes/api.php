<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;
use App\src\Interface\Http\Controllers\PostController;
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

// Public posts routes
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('{slug}', [PostController::class, 'show']);
});

// Admin posts routes
Route::prefix('admin')->middleware(['auth.jwt'])->group(function () {
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'adminIndex']);
        Route::post('/', [PostController::class, 'store'])->middleware('throttle:posts');
        Route::get('{id}', [PostController::class, 'adminShow'])->where('id', '[0-9]+');
        Route::put('{id}', [PostController::class, 'update'])->where('id', '[0-9]+')->middleware('throttle:posts');
        Route::delete('{id}', [PostController::class, 'destroy'])->where('id', '[0-9]+');
    });
});