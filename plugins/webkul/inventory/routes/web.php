<?php

use Illuminate\Support\Facades\Route;
use Webkul\Inventory\Http\Controllers\InventoryController;

Route::group(['prefix' => 'inventory', 'middleware' => ['web']], function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/{{id}}', [InventoryController::class, 'show'])->name('inventory.show');
});