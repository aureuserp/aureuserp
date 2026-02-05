<?php

use Illuminate\Support\Facades\Route;
use Webkul\Account\Http\Controllers\API\V1\ProductController;
use Webkul\Account\Http\Controllers\API\V1\ProductVariantController;

// Protected routes (require authentication)
Route::name('admin.api.v1.accounts.')->prefix('admin/api/v1/accounts')->middleware(['auth:sanctum'])->group(function () {
    Route::softDeletableApiResource('products', ProductController::class);

    Route::softDeletableApiResource('products.variants', ProductVariantController::class);
});
