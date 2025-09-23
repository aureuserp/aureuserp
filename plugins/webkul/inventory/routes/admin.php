<?php

use Illuminate\Support\Facades\Route;
use Webkul\Inventory\Http\Controllers\Admin\InventoryController;

Route::group(['prefix' => 'admin/inventory', 'middleware' => ['web', 'admin']], function () {
    Route::resource('inventory', InventoryController::class);
});