<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Webkul\Barcode\Livewire\Adjustments;
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
    $this->quantity = InventoryHelper::addProductQuantity($this->product, $this->stock, 6);
});

it('lists the stock row for the active company', function () {
    Livewire::test(Adjustments::class)
        ->assertOk()
        ->assertSee($this->product->name);
});

it('reports no change when the count already matches on hand', function () {
    Livewire::test(Adjustments::class)
        ->call('quickCountQuantity', $this->quantity->id)
        ->call('applyQuantityCount', $this->quantity->id)
        ->assertSet('noticeColor', 'info');

    expect((float) $this->quantity->fresh()->quantity)->toBe(6.0);
});

it('applies a counted difference to the on hand quantity', function () {
    Livewire::test(Adjustments::class)
        ->call('editQuantity', $this->quantity->id)
        ->call('setEditingQuantity', 6)
        ->call('adjustEditingQuantity', 1)
        ->call('adjustEditingQuantity', 1)
        ->call('confirmQuantityEdit')
        ->call('applyQuantityCount', $this->quantity->id)
        ->assertSet('noticeColor', 'success');

    expect((float) $this->quantity->fresh()->quantity)->toBe(8.0);
});

it('clears a pending count without touching the on hand quantity', function () {
    Livewire::test(Adjustments::class)
        ->call('editQuantity', $this->quantity->id)
        ->call('setEditingQuantity', 99)
        ->call('confirmQuantityEdit')
        ->call('clearQuantityCount', $this->quantity->id);

    $fresh = $this->quantity->fresh();

    expect((float) $fresh->quantity)->toBe(6.0)
        ->and((bool) $fresh->inventory_quantity_set)->toBeFalse()
        ->and((float) $fresh->counted_quantity)->toBe(0.0);
});

it('refuses to act on a quantity whose product is out of scope', function () {
    Livewire::test(Adjustments::class)
        ->call('applyQuantityCount', 99999999)
        ->assertSet('noticeColor', 'warning');
});

it('offers the apply action for a counted quantity of zero', function () {
    Livewire::test(Adjustments::class)
        ->call('editQuantity', $this->quantity->id)
        ->call('setEditingQuantity', 0)
        ->call('confirmQuantityEdit')
        ->assertSeeHtml('applyQuantityCount('.$this->quantity->id.')');
});

it('zeroes the on hand quantity when a count of zero is applied', function () {
    Livewire::test(Adjustments::class)
        ->call('editQuantity', $this->quantity->id)
        ->call('setEditingQuantity', 0)
        ->call('confirmQuantityEdit')
        ->call('applyQuantityCount', $this->quantity->id)
        ->assertSet('noticeColor', 'success');

    expect((float) ($this->quantity->fresh()?->quantity ?? 0.0))->toBe(0.0);
});
