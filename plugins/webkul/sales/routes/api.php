<?php

use Illuminate\Support\Facades\Route;
use Webkul\Sale\Http\Controllers\API\V1\OrderController;

Route::name('api.v1.admin.sales.')->prefix('api/v1/admin/sales')->group(function () {
    Route::apiResources([
        'orders' => OrderController::class,
    ]);
});//->middleware(['auth:sanctum']);
