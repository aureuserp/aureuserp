<?php

use Illuminate\Support\Facades\Route;
use Webkul\Product\Http\Controllers\API\V1\ProductController;

// Protected routes (require authentication)
Route::name('api.v1.admin.products.')->prefix('api/v1/admin/products')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('products', ProductController::class);

    Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
});
