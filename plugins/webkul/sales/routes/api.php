<?php

use Illuminate\Support\Facades\Route;
use Webkul\Sale\Http\Controllers\API\V1\OrderController;
use Webkul\Sale\Http\Controllers\API\V1\OrderLineController;

// Protected routes (require authentication)
Route::name('api.v1.admin.sales.')->prefix('api/v1/admin/sales')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('orders', OrderController::class);
    
    Route::apiResource('orders.lines', OrderLineController::class)
        ->only(['index', 'show']);
});
