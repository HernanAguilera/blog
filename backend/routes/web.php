<?php

use Illuminate\Support\Facades\Route;
use Blog\Interface\Http\Controllers\EditorController;

Route::get('/', function () {
    return view('welcome');
});

// Preview routes - accessible without authentication
Route::get('/preview/{token}', [EditorController::class, 'showPreview'])
    ->name('preview.show')
    ->middleware('throttle:preview-access');
