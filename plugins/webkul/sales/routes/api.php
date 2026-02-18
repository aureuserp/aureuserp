<?php

use Illuminate\Support\Facades\Route;
use Webkul\Sale\Http\Controllers\API\V1\OrderController;
use Webkul\Sale\Http\Controllers\API\V1\OrderLineController;
use Webkul\Sale\Http\Controllers\API\V1\TagController;

Route::name('admin.api.v1.sales.')->prefix('admin/api/v1/sales')->middleware(['auth:sanctum'])->group(function () {
    Route::softDeletableApiResource('orders', OrderController::class);
    Route::prefix('orders/{id}')->name('orders.')->group(function () {
        Route::post('confirm', [OrderController::class, 'confirm'])->name('confirm');
        Route::post('cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('set-as-quotation', [OrderController::class, 'setAsQuotation'])->name('set-as-quotation');
        Route::post('toggle-lock', [OrderController::class, 'toggleLock'])->name('toggle-lock');
    });

    Route::apiResource('orders.lines', OrderLineController::class)->only(['index', 'show']);

    Route::apiResource('tags', TagController::class);
});
