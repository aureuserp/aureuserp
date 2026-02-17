<?php

use Illuminate\Support\Facades\Route;
use Webkul\Account\Http\Controllers\API\V1\AccountController;
use Webkul\Account\Http\Controllers\API\V1\CashRoundingController;
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
use Webkul\Account\Http\Controllers\API\V1\RefundController;
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

    Route::apiResource('refunds', RefundController::class);
});
