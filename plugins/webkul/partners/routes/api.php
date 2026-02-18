<?php

use Illuminate\Support\Facades\Route;
use Webkul\Partner\Http\Controllers\API\V1\AddressController;
use Webkul\Partner\Http\Controllers\API\V1\BankAccountController;
use Webkul\Partner\Http\Controllers\API\V1\IndustryController;
use Webkul\Partner\Http\Controllers\API\V1\PartnerController;
use Webkul\Partner\Http\Controllers\API\V1\TagController;
use Webkul\Partner\Http\Controllers\API\V1\TitleController;

Route::name('admin.api.v1.partners.')->prefix('admin/api/v1/partners')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('titles', TitleController::class);

    Route::softDeletableApiResource('tags', TagController::class);

    Route::softDeletableApiResource('industries', IndustryController::class);

    Route::softDeletableApiResource('partners', PartnerController::class);

    Route::softDeletableApiResource('partners.addresses', AddressController::class);

    Route::softDeletableApiResource('partners.bank-accounts', BankAccountController::class);
});
