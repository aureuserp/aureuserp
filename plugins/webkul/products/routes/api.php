<?php

use Illuminate\Support\Facades\Route;
use Webkul\Product\Http\Controllers\API\V1\AttributeController;
use Webkul\Product\Http\Controllers\API\V1\AttributeOptionController;
use Webkul\Product\Http\Controllers\API\V1\CategoryController;
use Webkul\Product\Http\Controllers\API\V1\ProductController;
use Webkul\Product\Http\Controllers\API\V1\TagController;

// Protected routes (require authentication)
Route::name('admin.api.v1.products.')->prefix('admin/api/v1/products')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('tags', TagController::class);
    Route::post('tags/{id}/restore', [TagController::class, 'restore'])->name('tags.restore');
    Route::delete('tags/{id}/force', [TagController::class, 'forceDelete'])->name('tags.force-delete');

    Route::apiResource('attributes', AttributeController::class);
    Route::post('attributes/{id}/restore', [AttributeController::class, 'restore'])->name('attributes.restore');
    Route::delete('attributes/{id}/force', [AttributeController::class, 'forceDelete'])->name('attributes.force-delete');

    Route::apiResource('attributes.options', AttributeOptionController::class);

    Route::apiResource('products', ProductController::class);
    Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{id}/force', [ProductController::class, 'forceDelete'])->name('products.force-delete');
});
