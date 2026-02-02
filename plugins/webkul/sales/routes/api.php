<?php

use Illuminate\Support\Facades\Route;
use Webkul\Sale\Http\Controllers\API\V1\OrderController;
use Webkul\Sale\Http\Controllers\API\V1\OrderLineController;
use Webkul\Sale\Http\Controllers\API\V1\ProductController;

// Protected routes (require authentication)
Route::name('admin.api.v1.sales.')->prefix('admin/api/v1/sales')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('orders', OrderController::class);

    Route::apiResource('orders.lines', OrderLineController::class)
        ->only(['index', 'show']);

    Route::apiResource('products', ProductController::class);
    
    Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
});
