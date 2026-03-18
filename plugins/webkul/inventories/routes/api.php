<?php

use Illuminate\Support\Facades\Route;
use Webkul\Inventory\Http\Controllers\API\V1\DeliveryController;
use Webkul\Inventory\Http\Controllers\API\V1\DropshipController;
use Webkul\Inventory\Http\Controllers\API\V1\InternalTransferController;
use Webkul\Inventory\Http\Controllers\API\V1\LocationController;
use Webkul\Inventory\Http\Controllers\API\V1\LotController;
use Webkul\Inventory\Http\Controllers\API\V1\MoveController;
use Webkul\Inventory\Http\Controllers\API\V1\OperationTypeController;
use Webkul\Inventory\Http\Controllers\API\V1\PackageController;
use Webkul\Inventory\Http\Controllers\API\V1\PackageTypeController;
use Webkul\Inventory\Http\Controllers\API\V1\ProductController;
use Webkul\Inventory\Http\Controllers\API\V1\QuantityController;
use Webkul\Inventory\Http\Controllers\API\V1\ReceiptController;
use Webkul\Inventory\Http\Controllers\API\V1\RouteController;
use Webkul\Inventory\Http\Controllers\API\V1\RuleController;
use Webkul\Inventory\Http\Controllers\API\V1\ScrapController;
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

    Route::apiResource('receipts', ReceiptController::class);
    Route::prefix('receipts/{id}')->name('receipts.')->group(function () {
        Route::post('check-availability', [ReceiptController::class, 'checkAvailability'])->name('check-availability');
        Route::post('todo', [ReceiptController::class, 'todo'])->name('todo');
        Route::post('validate', [ReceiptController::class, 'validateTransfer'])->name('validate');
        Route::post('cancel', [ReceiptController::class, 'cancelTransfer'])->name('cancel');
        Route::post('return', [ReceiptController::class, 'returnTransfer'])->name('return');
    });

    Route::apiResource('deliveries', DeliveryController::class);
    Route::prefix('deliveries/{id}')->name('deliveries.')->group(function () {
        Route::post('check-availability', [DeliveryController::class, 'checkAvailability'])->name('check-availability');
        Route::post('todo', [DeliveryController::class, 'todo'])->name('todo');
        Route::post('validate', [DeliveryController::class, 'validateTransfer'])->name('validate');
        Route::post('cancel', [DeliveryController::class, 'cancelTransfer'])->name('cancel');
        Route::post('return', [DeliveryController::class, 'returnTransfer'])->name('return');
    });

    Route::apiResource('internal-transfers', InternalTransferController::class);
    Route::prefix('internal-transfers/{id}')->name('internal-transfers.')->group(function () {
        Route::post('check-availability', [InternalTransferController::class, 'checkAvailability'])->name('check-availability');
        Route::post('todo', [InternalTransferController::class, 'todo'])->name('todo');
        Route::post('validate', [InternalTransferController::class, 'validateTransfer'])->name('validate');
        Route::post('cancel', [InternalTransferController::class, 'cancelTransfer'])->name('cancel');
        Route::post('return', [InternalTransferController::class, 'returnTransfer'])->name('return');
    });

    Route::apiResource('dropships', DropshipController::class);
    Route::prefix('dropships/{id}')->name('dropships.')->group(function () {
        Route::post('check-availability', [DropshipController::class, 'checkAvailability'])->name('check-availability');
        Route::post('todo', [DropshipController::class, 'todo'])->name('todo');
        Route::post('validate', [DropshipController::class, 'validateTransfer'])->name('validate');
        Route::post('cancel', [DropshipController::class, 'cancelTransfer'])->name('cancel');
        Route::post('return', [DropshipController::class, 'returnTransfer'])->name('return');
    });

    Route::apiResource('quantities', QuantityController::class);
    Route::prefix('quantities/{id}')->name('quantities.')->group(function () {
        Route::post('apply', [QuantityController::class, 'apply'])->name('apply');
        Route::post('clear', [QuantityController::class, 'clear'])->name('clear');
    });

    Route::apiResource('scraps', ScrapController::class);
    Route::prefix('scraps/{id}')->name('scraps.')->group(function () {
        Route::post('validate', [ScrapController::class, 'validateScrap'])->name('validate');
    });

    Route::apiResource('moves', MoveController::class)->only(['index']);
});
