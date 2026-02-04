<?php

use Illuminate\Support\Facades\Route;
use Webkul\Account\Http\Controllers\API\V1\AttributeController;
use Webkul\Account\Http\Controllers\API\V1\AttributeOptionController;
use Webkul\Account\Http\Controllers\API\V1\CategoryController;
use Webkul\Account\Http\Controllers\API\V1\ProductAttributeController;
use Webkul\Account\Http\Controllers\API\V1\ProductController;
use Webkul\Account\Http\Controllers\API\V1\ProductVariantController;
use Webkul\Account\Http\Controllers\API\V1\TagController;

// Protected routes (require authentication)
Route::name('admin.api.v1.accounts.')->prefix('admin/api/v1/accounts')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('categories', CategoryController::class);

    Route::softDeletableApiResource('tags', TagController::class);

    Route::softDeletableApiResource('attributes', AttributeController::class);

    Route::apiResource('attributes.options', AttributeOptionController::class);

    Route::softDeletableApiResource('products', ProductController::class);

    Route::apiResource('products.attributes', ProductAttributeController::class);

    Route::softDeletableApiResource('products.variants', ProductVariantController::class);
});
