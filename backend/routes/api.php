<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\Admin\AdminCommentController;
use Blog\Interface\Http\Controllers\PostController;
use Blog\Interface\Http\Controllers\EditorController;
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
            ->middleware(['web', 'throttle:social-auth']);
        Route::get('{provider}/callback', [SocialAuthController::class, 'callback'])
            ->where('provider', 'google|facebook|twitter')
            ->middleware(['web', 'throttle:social-auth']);
    });
});

// Public posts routes
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('{slug}', [PostController::class, 'show']);

    // Public comment routes
    Route::prefix('{slug}/comments')->group(function () {
        Route::get('/', [CommentController::class, 'index']);
        Route::get('count', [CommentController::class, 'count']);
        Route::post('/', [CommentController::class, 'store'])
            ->middleware(['auth.jwt', 'throttle:comments']);
        Route::post('anonymous', [CommentController::class, 'storeAnonymous'])
            ->middleware(['throttle:comments', 'turnstile']);
    });
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

    // Admin comment moderation routes
    Route::prefix('comments')->group(function () {
        Route::get('pending', [AdminCommentController::class, 'pending']);

        // Single comment actions
        Route::patch('{id}/approve', [AdminCommentController::class, 'approve'])
            ->where('id', '[a-f0-9\-]{36}');
        Route::patch('{id}/reject', [AdminCommentController::class, 'reject'])
            ->where('id', '[a-f0-9\-]{36}');
        Route::patch('{id}/spam', [AdminCommentController::class, 'markAsSpam'])
            ->where('id', '[a-f0-9\-]{36}');
        Route::delete('{id}', [AdminCommentController::class, 'destroy'])
            ->where('id', '[a-f0-9\-]{36}');

        // Bulk actions
        Route::post('bulk-approve', [AdminCommentController::class, 'bulkApprove']);
        Route::post('bulk-reject', [AdminCommentController::class, 'bulkReject']);
        Route::post('bulk-delete', [AdminCommentController::class, 'bulkDelete']);
    });
});