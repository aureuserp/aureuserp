<?php

use Illuminate\Support\Facades\Route;
use Webkul\Sale\Http\Controllers\API\V1\AttributeController;
use Webkul\Sale\Http\Controllers\API\V1\AttributeOptionController;
use Webkul\Sale\Http\Controllers\API\V1\CategoryController;
use Webkul\Sale\Http\Controllers\API\V1\ProductAttributeController;
use Webkul\Sale\Http\Controllers\API\V1\ProductController;
use Webkul\Sale\Http\Controllers\API\V1\ProductVariantController;
use Webkul\Sale\Http\Controllers\API\V1\OrderController;
use Webkul\Sale\Http\Controllers\API\V1\OrderLineController;

Route::name('admin.api.v1.sales.')->prefix('admin/api/v1/sales')->middleware(['auth:sanctum'])->group(function () {
    Route::softDeletableApiResource('attributes', AttributeController::class);

    Route::apiResource('attributes.options', AttributeOptionController::class);

    Route::softDeletableApiResource('products', ProductController::class);

    Route::apiResource('products.attributes', ProductAttributeController::class);

    Route::softDeletableApiResource('products.variants', ProductVariantController::class);

    Route::apiResource('orders', OrderController::class);

    Route::apiResource('orders.lines', OrderLineController::class)->only(['index', 'show']);
});
