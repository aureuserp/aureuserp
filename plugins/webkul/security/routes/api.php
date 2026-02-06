<?php

use Illuminate\Support\Facades\Route;
use Webkul\Security\Http\Controllers\API\V1\AuthController;

// Authentication routes (public)
Route::prefix('admin/api/v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::prefix('admin/api/v1')->middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});
