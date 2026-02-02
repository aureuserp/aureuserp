<?php

use Illuminate\Support\Facades\Route;
use Webkul\Sale\Http\Controllers\API\V1\OrderController;
use Webkul\Sale\Http\Controllers\API\V1\OrderLineController;

Route::name('api.v1.admin.sales.')->prefix('api/v1/admin/sales')->group(function () {
    Route::apiResource('orders', OrderController::class);
    
    Route::apiResource('orders.lines', OrderLineController::class)
        ->only(['index', 'show']);
});//->middleware(['auth:sanctum']);
