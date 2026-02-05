<?php

use Illuminate\Support\Facades\Route;
use Webkul\Support\Http\Controllers\API\V1\CurrencyController;
use Webkul\Support\Http\Controllers\API\V1\CurrencyRateController;

Route::name('admin.api.v1.support.')->prefix('admin/api/v1/support')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('currency', CurrencyController::class);

    Route::apiResource('currency.rates', CurrencyRateController::class);
});
