<?php

use Illuminate\Support\Facades\Route;
use Webkul\Sale\Http\Controllers\API\V1\OrderController;
use Webkul\Sale\Http\Controllers\API\V1\OrderLineController;
use Webkul\Sale\Http\Controllers\API\V1\TagController;

Route::name('admin.api.v1.sales.')->prefix('admin/api/v1/sales')->middleware(['auth:sanctum'])->group(function () {
    Route::softDeletableApiResource('orders', OrderController::class);

    Route::apiResource('orders.lines', OrderLineController::class)->only(['index', 'show']);

    Route::apiResource('tags', TagController::class);
});
