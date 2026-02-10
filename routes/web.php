<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $records = [\Webkul\Inventory\Models\Operation::find(9)];

    return view('inventories::filament.clusters.operations.actions.print-picking-operations', compact('records'));
})->name('home');

if (! request()->getRequestUri() == '/login') {
    Route::redirect('/login', '/admin/login')
        ->name('login');
}
