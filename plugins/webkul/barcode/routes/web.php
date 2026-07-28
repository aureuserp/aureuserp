<?php

use Filament\Http\Middleware\SetUpPanel;
use Illuminate\Support\Facades\Route;
use Webkul\Barcode\Http\Middleware\Authenticate;
use Webkul\NativephpRemote\Http\Middleware\PersistNativeShell;
use Webkul\NativephpRemote\Http\Middleware\RenderHostedNativeUi;
use Webkul\Barcode\Livewire\Adjustments;
use Webkul\Barcode\Livewire\Auth\Login;
use Webkul\Barcode\Livewire\Dashboard;
use Webkul\Barcode\Livewire\Operation;
use Webkul\Barcode\Livewire\Transfers;

Route::middleware(['web', PersistNativeShell::class, RenderHostedNativeUi::class])->group(function (): void {
    Route::get('barcode', function () {
        if (auth()->check()) {
            return redirect()->route('barcode.dashboard');
        }

        return redirect()->route('barcode.login');
    });
});

Route::middleware(['web', PersistNativeShell::class, RenderHostedNativeUi::class, SetUpPanel::class.':admin'])->prefix('admin/barcode')->name('barcode.')->group(function (): void {
    Route::get('/login', Login::class)->name('login');

    Route::middleware([Authenticate::class])->group(function (): void {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/inventory-adjustments', Adjustments::class)->name('adjustments');
        Route::get('/operations/{operationType}', Transfers::class)->name('transfers');
        Route::get('/operations/{operationType}/transfers/{operation}', Operation::class)->name('operation');
    });
});
