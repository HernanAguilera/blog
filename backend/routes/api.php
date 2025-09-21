<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;
use App\src\Interface\Http\Controllers\PostController;
use App\src\Interface\Http\Controllers\EditorController;
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

// Public preview route (no auth required)
Route::get('preview/{token}', [EditorController::class, 'showPreview'])
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:preview-access');

// Admin posts routes
Route::prefix('admin')->middleware(['auth.jwt'])->group(function () {
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'adminIndex']);
        Route::post('/', [PostController::class, 'store'])->middleware('throttle:posts');
        Route::get('{id}', [PostController::class, 'adminShow'])->where('id', '[0-9]+');
        Route::put('{id}', [PostController::class, 'update'])->where('id', '[0-9]+')->middleware('throttle:posts');
        Route::delete('{id}', [PostController::class, 'destroy'])->where('id', '[0-9]+');
        Route::patch('{id}/transition', [PostController::class, 'changeStatus'])->where('id', '[0-9]+')->middleware('throttle:posts');
        Route::get('{id}/transitions', [PostController::class, 'getTransitions'])->where('id', '[0-9]+');

        // Editor endpoints
        Route::post('autosave', [EditorController::class, 'autoSave'])->middleware('throttle:autosave');
        Route::post('preview', [EditorController::class, 'generatePreview'])->middleware('throttle:preview');

        // Debug endpoint
        Route::get('debug-auth', function() {
            return response()->json([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'token_valid' => \Illuminate\Support\Facades\Auth::check()
            ]);
        });

        // Draft management
        Route::get('drafts', [EditorController::class, 'getDrafts']);
        Route::get('drafts/{id}', [EditorController::class, 'restoreDraft'])->where('id', '[0-9]+');
    });
});