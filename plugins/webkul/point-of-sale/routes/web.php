<?php

use Filament\Http\Middleware\Authenticate;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Webkul\PointOfSale\Http\Controllers\API\V1\BootController;
use Webkul\PointOfSale\Http\Controllers\API\V1\ClosingControlController;
use Webkul\PointOfSale\Http\Controllers\API\V1\DraftCancelController;
use Webkul\PointOfSale\Http\Controllers\API\V1\DraftController;
use Webkul\PointOfSale\Http\Controllers\API\V1\FloorPlanController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderCancelController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderHistoryController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderInvoiceController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderInvoicePdfController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderRefundController;
use Webkul\PointOfSale\Http\Controllers\API\V1\OrderSyncController;
use Webkul\PointOfSale\Http\Controllers\API\V1\ReceiptController;
use Webkul\PointOfSale\Http\Controllers\API\V1\RegisterListController;
use Webkul\PointOfSale\Http\Controllers\API\V1\SalesDetailsController;
use Webkul\PointOfSale\Http\Controllers\API\V1\SessionController;
use Webkul\PointOfSale\Http\Controllers\API\V1\TerminalProductController;
use Webkul\PointOfSale\Http\Controllers\LogoutController;
use Webkul\PointOfSale\Http\Controllers\PwaController;

Route::post('pos/sign-out', LogoutController::class)
    ->middleware(['web'])
    ->name('point-of-sale.pos.logout');

Route::name('point-of-sale.till.')
    ->prefix('pos/till')
    ->middleware(['web', Authenticate::class, SubstituteBindings::class])
    ->group(function (): void {
        Route::post('orders/sync', [OrderSyncController::class, 'store'])->name('orders.sync');
        Route::post('orders/cancel', [OrderCancelController::class, 'store'])->name('orders.cancel');
        Route::post('orders/drafts', [DraftController::class, 'store'])->name('orders.drafts.store');
        Route::get('sessions/{session}/drafts', [DraftController::class, 'index'])->name('sessions.drafts');
        Route::post('configs/{config}/floors', [FloorPlanController::class, 'store'])->name('configs.floors.store');
        Route::put('floors/{floor}', [FloorPlanController::class, 'update'])->name('floors.update');
        Route::delete('floors/{floor}', [FloorPlanController::class, 'destroy'])->name('floors.destroy');
        Route::post('floors/{floor}/background', [FloorPlanController::class, 'storeBackground'])->name('floors.background.store');
        Route::delete('floors/{floor}/background', [FloorPlanController::class, 'destroyBackground'])->name('floors.background.destroy');
        Route::get('sessions/{session}/boot', [BootController::class, 'show'])->name('sessions.boot');
        Route::post('configs/{config}/products', [TerminalProductController::class, 'store'])->name('configs.products.store');

        Route::get('registers', [RegisterListController::class, 'index'])->name('registers');
        Route::post('registers/{config}/discard', [RegisterListController::class, 'discard'])->name('registers.discard');
        Route::get('configs/{config}/session', [SessionController::class, 'current'])->name('configs.session');
        Route::post('configs/{config}/session', [SessionController::class, 'open'])->name('configs.session.open');

        Route::get('sessions/{session}/orders', [OrderHistoryController::class, 'index'])->name('sessions.orders');
        Route::post('sessions/{session}/opening-control', [SessionController::class, 'confirmOpeningControl'])->name('sessions.opening-control');
        Route::get('sessions/{session}/closing-control-report', [ClosingControlController::class, 'show'])->name('sessions.closing-control.report');
        Route::post('sessions/{session}/closing-control', [SessionController::class, 'requestClosing'])->name('sessions.closing-control');
        Route::post('sessions/{session}/close', [SessionController::class, 'close'])->name('sessions.close');
        Route::post('sessions/{session}/cash-movements', [SessionController::class, 'cashMovement'])->name('sessions.cash-movements');
        Route::post('sessions/{session}/cancel-draft-orders', [DraftCancelController::class, 'store'])->name('sessions.cancel-draft-orders');
        Route::get('sessions/{session}/sales-details', [SalesDetailsController::class, 'show'])->name('sessions.sales-details');

        Route::get('orders/{order}/refundable-lines', [OrderRefundController::class, 'refundable'])->name('orders.refundable');
        Route::post('orders/{order}/refund', [OrderRefundController::class, 'store'])->name('orders.refund');
        Route::post('orders/{order}/invoice', [OrderInvoiceController::class, 'store'])->name('orders.invoice');
        Route::get('orders/{order}/invoice-pdf', [OrderInvoicePdfController::class, 'show'])->name('orders.invoice-pdf');
        Route::get('orders/{order}/receipt', [ReceiptController::class, 'show'])->name('orders.receipt');
    });

Route::name('point-of-sale.pwa.')
    ->prefix('pos')
    ->middleware(['web', SubstituteBindings::class])
    ->group(function (): void {
        Route::get('service-worker.js', [PwaController::class, 'serviceWorker'])->name('service-worker');
        Route::get('manifest.webmanifest', [PwaController::class, 'manifest'])->name('manifest');
        Route::get('{config}/manifest.webmanifest', [PwaController::class, 'manifest'])->name('manifest.config');
    });
