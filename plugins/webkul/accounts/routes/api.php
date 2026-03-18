<?php

use Illuminate\Support\Facades\Route;
use Webkul\Account\Http\Controllers\API\V1\AccountController;
use Webkul\Account\Http\Controllers\API\V1\BillController;
use Webkul\Account\Http\Controllers\API\V1\CashRoundingController;
use Webkul\Account\Http\Controllers\API\V1\RefundController;
use Webkul\Account\Http\Controllers\API\V1\CategoryController;
use Webkul\Account\Http\Controllers\API\V1\CustomerController;
use Webkul\Account\Http\Controllers\API\V1\FiscalPositionController;
use Webkul\Account\Http\Controllers\API\V1\IncotermController;
use Webkul\Account\Http\Controllers\API\V1\InvoiceController;
use Webkul\Account\Http\Controllers\API\V1\JournalController;
use Webkul\Account\Http\Controllers\API\V1\PaymentDueTermController;
use Webkul\Account\Http\Controllers\API\V1\PaymentTermController;
use Webkul\Account\Http\Controllers\API\V1\ProductController;
use Webkul\Account\Http\Controllers\API\V1\ProductVariantController;
use Webkul\Account\Http\Controllers\API\V1\CreditNoteController;
use Webkul\Account\Http\Controllers\API\V1\TaxController;
use Webkul\Account\Http\Controllers\API\V1\TaxGroupController;
use Webkul\Account\Http\Controllers\API\V1\VendorController;

// Protected routes (require authentication)
Route::name('admin.api.v1.accounts.')->prefix('admin/api/v1/accounts')->middleware(['auth:sanctum'])->group(function () {
    Route::softDeletableApiResource('payment-terms', PaymentTermController::class);

    Route::apiResource('payment-terms.due-terms', PaymentDueTermController::class);

    Route::softDeletableApiResource('incoterms', IncotermController::class);

    Route::apiResource('accounts', AccountController::class);

    Route::apiResource('journals', JournalController::class);

    Route::apiResource('fiscal-positions', FiscalPositionController::class);

    Route::apiResource('cash-roundings', CashRoundingController::class);

    Route::apiResource('tax-groups', TaxGroupController::class);

    Route::apiResource('taxes', TaxController::class);

    Route::apiResource('categories', CategoryController::class);

    Route::softDeletableApiResource('products', ProductController::class);

    Route::softDeletableApiResource('products.variants', ProductVariantController::class);

    Route::softDeletableApiResource('customers', CustomerController::class);

    Route::softDeletableApiResource('vendors', VendorController::class);

    Route::apiResource('invoices', InvoiceController::class);
    Route::prefix('invoices/{id}')->name('invoices.')->group(function () {
        Route::post('confirm', [InvoiceController::class, 'confirm'])->name('confirm');
        Route::post('cancel', [InvoiceController::class, 'cancel'])->name('cancel');
        Route::post('pay', [InvoiceController::class, 'pay'])->name('pay');
        Route::post('reverse', [InvoiceController::class, 'reverse'])->name('reverse');
        Route::post('reset-to-draft', [InvoiceController::class, 'resetToDraft'])->name('reset-to-draft');
        Route::post('set-as-checked', [InvoiceController::class, 'setAsChecked'])->name('set-as-checked');
    });

    Route::apiResource('credit-notes', CreditNoteController::class);
    Route::prefix('credit-notes/{id}')->name('credit-notes.')->group(function () {
        Route::post('confirm', [CreditNoteController::class, 'confirm'])->name('confirm');
        Route::post('cancel', [CreditNoteController::class, 'cancel'])->name('cancel');
        Route::post('pay', [CreditNoteController::class, 'pay'])->name('pay');
        Route::post('reset-to-draft', [CreditNoteController::class, 'resetToDraft'])->name('reset-to-draft');
        Route::post('set-as-checked', [CreditNoteController::class, 'setAsChecked'])->name('set-as-checked');
    });

    Route::apiResource('bills', BillController::class);
    Route::prefix('bills/{id}')->name('bills.')->group(function () {
        Route::post('confirm', [BillController::class, 'confirm'])->name('confirm');
        Route::post('cancel', [BillController::class, 'cancel'])->name('cancel');
        Route::post('pay', [BillController::class, 'pay'])->name('pay');
        Route::post('reverse', [BillController::class, 'reverse'])->name('reverse');
        Route::post('reset-to-draft', [BillController::class, 'resetToDraft'])->name('reset-to-draft');
        Route::post('set-as-checked', [BillController::class, 'setAsChecked'])->name('set-as-checked');
    });

    Route::apiResource('refunds', RefundController::class);
    Route::prefix('refunds/{id}')->name('refunds.')->group(function () {
        Route::post('confirm', [RefundController::class, 'confirm'])->name('confirm');
        Route::post('cancel', [RefundController::class, 'cancel'])->name('cancel');
        Route::post('pay', [RefundController::class, 'pay'])->name('pay');
        Route::post('reset-to-draft', [RefundController::class, 'resetToDraft'])->name('reset-to-draft');
        Route::post('set-as-checked', [RefundController::class, 'setAsChecked'])->name('set-as-checked');
    });
});
