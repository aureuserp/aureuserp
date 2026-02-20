<?php

use Illuminate\Support\Facades\Route;
use Webkul\Purchase\Http\Controllers\API\V1\PurchaseAgreementController;
use Webkul\Purchase\Http\Controllers\API\V1\PurchaseAgreementLineController;
use Webkul\Purchase\Http\Controllers\API\V1\PurchaseOrderBillController;
use Webkul\Purchase\Http\Controllers\API\V1\PurchaseOrderController;
use Webkul\Purchase\Http\Controllers\API\V1\PurchaseOrderLineController;
use Webkul\Purchase\Http\Controllers\API\V1\PurchaseOrderReceiptController;
use Webkul\Purchase\Http\Controllers\API\V1\VendorPriceListController;

Route::name('admin.api.v1.purchases.')->prefix('admin/api/v1/purchases')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('vendor-price-lists', VendorPriceListController::class);

    Route::apiResource('purchase-orders', PurchaseOrderController::class);
    Route::prefix('purchase-orders/{id}')->name('purchase-orders.')->group(function () {
        Route::post('confirm', [PurchaseOrderController::class, 'confirm'])->name('confirm');
        Route::post('cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');
        Route::post('draft', [PurchaseOrderController::class, 'draft'])->name('draft');
        Route::post('toggle-lock', [PurchaseOrderController::class, 'toggleLock'])->name('toggle-lock');
        Route::post('confirm-receipt-date', [PurchaseOrderController::class, 'confirmReceiptDate'])->name('confirm-receipt-date');
    });
    Route::apiResource('purchase-orders.lines', PurchaseOrderLineController::class)->only(['index', 'show']);
    Route::apiResource('purchase-orders.receipts', PurchaseOrderReceiptController::class)->only(['index']);
    Route::apiResource('purchase-orders.bills', PurchaseOrderBillController::class)->only(['index']);

    Route::softDeletableApiResource('purchase-agreements', PurchaseAgreementController::class);
    Route::prefix('purchase-agreements/{id}')->name('purchase-agreements.')->group(function () {
        Route::post('confirm', [PurchaseAgreementController::class, 'confirm'])->name('confirm');
        Route::post('close', [PurchaseAgreementController::class, 'close'])->name('close');
        Route::post('cancel', [PurchaseAgreementController::class, 'cancel'])->name('cancel');
    });
    Route::apiResource('purchase-agreements.lines', PurchaseAgreementLineController::class)->only(['index', 'show']);
});
