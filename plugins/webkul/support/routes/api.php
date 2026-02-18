<?php

use Illuminate\Support\Facades\Route;
use Webkul\Support\Http\Controllers\API\V1\BankController;
use Webkul\Support\Http\Controllers\API\V1\CountryController;
use Webkul\Support\Http\Controllers\API\V1\CurrencyController;
use Webkul\Support\Http\Controllers\API\V1\CurrencyRateController;
use Webkul\Support\Http\Controllers\API\V1\StateController;
use Webkul\Support\Http\Controllers\API\V1\UOMCategoryController;
use Webkul\Support\Http\Controllers\API\V1\UOMController;

Route::name('admin.api.v1.support.')->prefix('admin/api/v1/support')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('currencies', CurrencyController::class);

    Route::apiResource('currencies.rates', CurrencyRateController::class);

    Route::softDeletableApiResource('banks', BankController::class);

    Route::apiResource('countries', CountryController::class)->only(['index', 'show']);

    Route::apiResource('states', StateController::class);

    Route::apiResource('uom-categories', UOMCategoryController::class);

    Route::softDeletableApiResource('uom-categories.uoms', UOMController::class);
});
