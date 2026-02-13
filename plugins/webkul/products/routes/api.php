<?php

use Illuminate\Support\Facades\Route;
use Webkul\Product\Http\Controllers\API\V1\AttributeController;
use Webkul\Product\Http\Controllers\API\V1\AttributeOptionController;
use Webkul\Product\Http\Controllers\API\V1\CategoryController;
use Webkul\Product\Http\Controllers\API\V1\ProductAttributeController;
use Webkul\Product\Http\Controllers\API\V1\ProductController;
use Webkul\Product\Http\Controllers\API\V1\ProductVariantController;
use Webkul\Product\Http\Controllers\API\V1\PackagingController;
use Webkul\Product\Http\Controllers\API\V1\TagController;

// Protected routes (require authentication)
Route::name('admin.api.v1.products.')->prefix('admin/api/v1/products')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('categories', CategoryController::class);

    Route::softDeletableApiResource('tags', TagController::class);

    Route::softDeletableApiResource('attributes', AttributeController::class);

    Route::apiResource('attributes.options', AttributeOptionController::class);

    Route::softDeletableApiResource('products', ProductController::class);

    Route::apiResource('products.attributes', ProductAttributeController::class);

    Route::softDeletableApiResource('products.variants', ProductVariantController::class);

    Route::apiResource('packagings', PackagingController::class);
});
