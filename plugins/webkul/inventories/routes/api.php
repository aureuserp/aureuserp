<?php

use Illuminate\Support\Facades\Route;
use Webkul\Inventory\Http\Controllers\API\V1\LocationController;
use Webkul\Inventory\Http\Controllers\API\V1\LotController;
use Webkul\Inventory\Http\Controllers\API\V1\OperationTypeController;
use Webkul\Inventory\Http\Controllers\API\V1\ProductController;
use Webkul\Inventory\Http\Controllers\API\V1\PackageTypeController;
use Webkul\Inventory\Http\Controllers\API\V1\PackageController;
use Webkul\Inventory\Http\Controllers\API\V1\RouteController;
use Webkul\Inventory\Http\Controllers\API\V1\RuleController;
use Webkul\Inventory\Http\Controllers\API\V1\StorageCategoryController;
use Webkul\Inventory\Http\Controllers\API\V1\TagController;
use Webkul\Inventory\Http\Controllers\API\V1\WarehouseController;

Route::name('admin.api.v1.inventories.')->prefix('admin/api/v1/inventories')->middleware(['auth:sanctum'])->group(function () {
    Route::softDeletableApiResource('warehouses', WarehouseController::class);
    Route::softDeletableApiResource('locations', LocationController::class);
    Route::softDeletableApiResource('routes', RouteController::class);
    Route::softDeletableApiResource('operation-types', OperationTypeController::class);
    Route::softDeletableApiResource('rules', RuleController::class);
    Route::apiResource('storage-categories', StorageCategoryController::class);
    Route::apiResource('package-types', PackageTypeController::class);
    Route::softDeletableApiResource('tags', TagController::class);
    Route::softDeletableApiResource('products', ProductController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('lots', LotController::class);
});
