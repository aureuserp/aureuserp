<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Filament\Clusters\Operations\Actions as OperationActions;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\CreateDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\EditDelivery;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ListDeliveries;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ViewDelivery;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    foreach (['products', 'inventories'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
});

it('forbids listing deliveries without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListDeliveries::class)->assertForbidden();
});

it('lists deliveries for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery']);

    Livewire::test(ListDeliveries::class)->assertOk();
});

it('lists a delivery with its key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Livewire::test(ListDeliveries::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$delivery])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state')
        ->assertCanRenderTableColumn('scheduled_at')
        ->assertCanRenderTableColumn('company.name')
        ->assertTableColumnExists('sourceLocation.full_name')
        ->assertTableColumnExists('destinationLocation.full_name');
});

it('finds a delivery by searching its reference', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Livewire::test(ListDeliveries::class)
        ->searchTable($delivery->name)
        ->assertCanSeeTableRecords([$delivery]);
});

it('renders the delivery create page', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'create_inventory_delivery']);

    Livewire::test(CreateDelivery::class)->assertOk();
});

it('renders the delivery edit page', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'update_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Livewire::test(EditDelivery::class, ['record' => $delivery->id])->assertOk();
});

it('warns about insufficient stock on a draft delivery that exceeds the free quantity', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'update_inventory_delivery']);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 11]]);

    expect((float) $delivery->moves->first()->forecast_availability)->toBe(-1.0);

    Livewire::test(EditDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertSee(InventoryHelper::insufficientStockTooltip());
});

it('does not warn about insufficient stock on a draft delivery that is covered', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'update_inventory_delivery']);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    expect((float) $delivery->moves->first()->forecast_availability)->toBe(10.0);

    Livewire::test(EditDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('does not warn about insufficient stock on a delivery that is exactly covered', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'update_inventory_delivery']);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Livewire::test(EditDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('warns about insufficient stock on a draft delivery when there is no stock at all', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'update_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    expect((float) $delivery->moves->first()->forecast_availability)->toBe(-5.0);

    Livewire::test(EditDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertSee(InventoryHelper::insufficientStockTooltip());
});

it('stops warning about insufficient stock once a quantity has been picked', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'update_inventory_delivery']);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 11]]);

    Livewire::test(EditDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertSee(InventoryHelper::insufficientStockTooltip());

    InventoryHelper::pick($delivery->moves->first(), 10);

    Livewire::test(EditDelivery::class, ['record' => $delivery->refresh()->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('does not warn about insufficient stock on an outgoing delivery that has left draft', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'update_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Inventory::confirmTransfer($delivery);

    Livewire::test(EditDelivery::class, ['record' => $delivery->refresh()->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('offers the todo action on a draft delivery', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'view_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Livewire::test(ViewDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\TodoAction::class)
        ->assertActionHidden(OperationActions\CheckAvailabilityAction::class);
});

it('offers the check availability action once the delivery is confirmed', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'view_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Inventory::confirmTransfer($delivery);

    expect($delivery->refresh()->state)->toBe(OperationState::CONFIRMED);

    Livewire::test(ViewDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\CheckAvailabilityAction::class)
        ->assertActionHidden(OperationActions\TodoAction::class);
});

it('offers the return action only once the delivery is done', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'view_inventory_delivery']);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Livewire::test(ViewDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertActionHidden(OperationActions\ReturnAction::class);

    Inventory::confirmTransfer($delivery);
    Inventory::completeTransfer($delivery->refresh());

    expect($delivery->refresh()->state)->toBe(OperationState::DONE);

    Livewire::test(ViewDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\ReturnAction::class)
        ->assertActionHidden(OperationActions\ValidateAction::class)
        ->assertActionHidden(OperationActions\CancelAction::class);
});

it('cancels a delivery through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_inventory_delivery', 'view_inventory_delivery']);

    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Livewire::test(ViewDelivery::class, ['record' => $delivery->id])
        ->assertOk()
        ->callAction(OperationActions\CancelAction::class);

    expect($delivery->refresh()->state)->toBe(OperationState::CANCELED);
});

it('lists a delivery to a user of the owning company', function () {
    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    FilamentHelper::actingAsCompanyUser(InventoryHelper::company(), ['view_any_inventory_delivery']);

    Livewire::test(ListDeliveries::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$delivery]);
});

it('hides a delivery owned by another company from the list', function () {
    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), ['view_any_inventory_delivery']);

    Livewire::test(ListDeliveries::class)
        ->assertOk()
        ->assertCanNotSeeTableRecords([$delivery]);
});

it('does not find a delivery of another company by searching its reference', function () {
    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), ['view_any_inventory_delivery']);

    Livewire::test(ListDeliveries::class)
        ->searchTable($delivery->name)
        ->assertCanNotSeeTableRecords([$delivery]);
});

it('refuses to open a delivery owned by another company', function () {
    $delivery = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), [
        'view_any_inventory_delivery',
        'view_inventory_delivery',
    ]);

    expect(fn () => Livewire::test(ViewDelivery::class, ['record' => $delivery->id]))
        ->toThrow(ModelNotFoundException::class);
});
