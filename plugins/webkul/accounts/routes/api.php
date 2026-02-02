<?php

use Illuminate\Support\Facades\Route;
use Webkul\Account\Http\Controllers\API\V1\ProductController;

// Protected routes (require authentication)
Route::name('api.v1.admin.accounts.')->prefix('api/v1/admin/accounts')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('products', ProductController::class);
    
    Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
});
