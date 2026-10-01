<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Webkul\Barcode\Livewire\Operation as BarcodeOperation;
use Webkul\Barcode\Livewire\Transfers;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../inventories/tests/Helpers/InventoryHelper.php';

beforeEach(function () {
    foreach (['products', 'inventories', 'barcode'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    foreach (['products', 'inventories', 'barcode'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    if (! Route::has('barcode.dashboard')) {
        require base_path('plugins/webkul/barcode/routes/web.php');

        Route::getRoutes()->refreshNameLookups();
        Route::getRoutes()->refreshActionLookups();
    }

    FilamentHelper::actingAs(['page_barcode_launch_barcode'])->forceFill(['is_active' => true])->saveQuietly();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;

    InventoryHelper::stockUp($this->product, $this->stock, 20);

    $this->delivery = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 5],
    ]);

    Inventory::confirmTransfer($this->delivery);

    $this->delivery = $this->delivery->fresh();
    $this->operationType = $this->delivery->operationType;
});

it('lists only transfers that are open', function () {
    $draft = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 2],
    ]);

    Livewire::test(Transfers::class, ['operationType' => $this->operationType])
        ->assertOk()
        ->assertSee($this->delivery->name)
        ->assertDontSee($draft->name);
});

it('offers no actions on a draft transfer', function () {
    $this->delivery->update(['state' => OperationState::DRAFT]);

    $view = Livewire::test(BarcodeOperation::class, [
        'operationType' => $this->operationType,
        'operation'     => $this->delivery->fresh(),
    ])->assertOk();

    $view->assertViewHas('actions', []);
    $view->assertViewHas('blockedNotice', __('barcode::app.actions.draft-blocked'));
});

it('offers no actions on a completed transfer', function () {
    $this->delivery->update(['state' => OperationState::DONE]);

    Livewire::test(BarcodeOperation::class, [
        'operationType' => $this->operationType,
        'operation'     => $this->delivery->fresh(),
    ])->assertOk()->assertViewHas('actions', []);
});

it('keeps an open transfer out of the draft block', function () {
    expect($this->delivery->state)->not->toBe(OperationState::DRAFT);
});

it('offers validate and cancel on an open transfer', function () {
    $actions = Livewire::test(BarcodeOperation::class, [
        'operationType' => $this->operationType,
        'operation'     => $this->delivery->fresh(),
    ])->assertOk()->viewData('actions');

    expect(collect($actions)->pluck('key')->all())->toBe(['validate', 'cancel']);
});

it('refuses to validate a draft transfer', function () {
    $this->delivery->update(['state' => OperationState::DRAFT]);

    Livewire::test(BarcodeOperation::class, [
        'operationType' => $this->operationType,
        'operation'     => $this->delivery->fresh(),
    ])->call('executeAction', 'validate')
        ->assertSet('notice', __('barcode::app.actions.draft-blocked'));

    expect($this->delivery->fresh()->state)->toBe(OperationState::DRAFT);
});
