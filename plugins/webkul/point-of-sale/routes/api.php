<?php

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Webkul\PointOfSale\Http\Controllers\API\V1\BootController;
use Webkul\PointOfSale\Http\Controllers\API\V1\CatalogController;
use Webkul\PointOfSale\Http\Controllers\API\V1\ClosingControlController;
use Webkul\PointOfSale\Http\Controllers\API\V1\DraftCancelController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderHistoryController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderInvoiceController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderInvoicePdfController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderRefundController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderSyncController;
use Webkul\PointOfSale\Http\Controllers\API\V1\ReceiptController;
use Webkul\PointOfSale\Http\Controllers\API\V1\RegisterListController;
use Webkul\PointOfSale\Http\Controllers\API\V1\SalesDetailsController;
use Webkul\PointOfSale\Http\Controllers\API\V1\SessionController;

Route::name('admin.api.v1.point-of-sale.')->prefix('admin/api/v1/point-of-sale')->middleware([SubstituteBindings::class, 'auth:sanctum'])->group(function () {
    Route::post('orders/sync', [OrderSyncController::class, 'store'])->name('orders.sync');
    Route::get('configs/{config}/catalog', [CatalogController::class, 'show'])->name('configs.catalog');
    Route::get('sessions/{session}/boot', [BootController::class, 'show'])->name('sessions.boot');
    Route::get('registers', [RegisterListController::class, 'index'])->name('registers');
    Route::post('registers/{config}/discard', [RegisterListController::class, 'discard'])->name('registers.discard');
    Route::get('configs/{config}/session', [SessionController::class, 'current'])->name('configs.session');
    Route::post('configs/{config}/session', [SessionController::class, 'open'])->name('configs.session.open');
    Route::post('sessions/{session}/opening-control', [SessionController::class, 'confirmOpeningControl'])->name('sessions.opening-control');
    Route::post('sessions/{session}/closing-control', [SessionController::class, 'requestClosing'])->name('sessions.closing-control');
    Route::post('sessions/{session}/close', [SessionController::class, 'close'])->name('sessions.close');
    Route::post('sessions/{session}/cash-movements', [SessionController::class, 'cashMovement'])->name('sessions.cash-movements');

    Route::get('sessions/{session}/orders', [OrderHistoryController::class, 'index'])->name('sessions.orders');
    Route::get('sessions/{session}/closing-control-report', [ClosingControlController::class, 'show'])->name('sessions.closing-control.report');
    Route::post('sessions/{session}/cancel-draft-orders', [DraftCancelController::class, 'store'])->name('sessions.cancel-draft-orders');
    Route::get('sessions/{session}/sales-details', [SalesDetailsController::class, 'show'])->name('sessions.sales-details');

    Route::get('orders/{order}/refundable-lines', [OrderRefundController::class, 'refundable'])->name('orders.refundable');
    Route::post('orders/{order}/refund', [OrderRefundController::class, 'store'])->name('orders.refund');
    Route::post('orders/{order}/invoice', [OrderInvoiceController::class, 'store'])->name('orders.invoice');
    Route::get('orders/{order}/invoice-pdf', [OrderInvoicePdfController::class, 'show'])->name('orders.invoice-pdf');
    Route::get('orders/{order}/receipt', [ReceiptController::class, 'show'])->name('orders.receipt');
});
