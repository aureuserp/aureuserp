<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Filament\Clusters\Operations\Actions as OperationActions;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages\CreateInternal;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages\EditInternal;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages\ListInternals;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource\Pages\ViewInternal;
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

    InventoryHelper::enableLocations();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->shelf = InventoryHelper::sublocation($this->stock, 'Shelf A');
});

function internalTransferRecord($context)
{
    return InventoryHelper::internalTransfer(
        $context->warehouse,
        $context->stock,
        $context->shelf,
        [[$context->product, 5]],
    );
}

it('forbids listing internal transfers without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListInternals::class)->assertForbidden();
});

it('lists internal transfers for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal']);

    Livewire::test(ListInternals::class)->assertOk();
});

it('lists an internal transfer with its key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal']);

    $transfer = internalTransferRecord($this);

    Livewire::test(ListInternals::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$transfer])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state')
        ->assertCanRenderTableColumn('company.name')
        ->assertTableColumnExists('sourceLocation.full_name')
        ->assertTableColumnExists('destinationLocation.full_name');
});

it('finds an internal transfer by searching its reference', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal']);

    $transfer = internalTransferRecord($this);

    Livewire::test(ListInternals::class)
        ->searchTable($transfer->name)
        ->assertCanSeeTableRecords([$transfer]);
});

it('renders the internal transfer create page', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'create_inventory_internal']);

    Livewire::test(CreateInternal::class)->assertOk();
});

it('renders the internal transfer edit page', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'update_inventory_internal']);

    $transfer = internalTransferRecord($this);

    Livewire::test(EditInternal::class, ['record' => $transfer->id])->assertOk();
});

it('warns about insufficient stock on a draft internal transfer that exceeds the source stock', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'update_inventory_internal']);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $transfer = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 11]]);

    expect((float) $transfer->moves->first()->forecast_availability)->toBe(-1.0);

    Livewire::test(EditInternal::class, ['record' => $transfer->id])
        ->assertOk()
        ->assertSee(InventoryHelper::insufficientStockTooltip());
});

it('does not warn about insufficient stock on a covered draft internal transfer', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'update_inventory_internal']);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $transfer = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 5]]);

    expect((float) $transfer->moves->first()->forecast_availability)->toBe(10.0);

    Livewire::test(EditInternal::class, ['record' => $transfer->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('warns about insufficient stock when the stock sits in another warehouse', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'update_inventory_internal']);

    $otherWarehouse = InventoryHelper::warehouse();

    InventoryHelper::stockUp($this->product, $otherWarehouse->lotStockLocation, 100);

    $transfer = internalTransferRecord($this);

    expect((float) $transfer->moves->first()->forecast_availability)->toBe(-5.0);

    Livewire::test(EditInternal::class, ['record' => $transfer->id])
        ->assertOk()
        ->assertSee(InventoryHelper::insufficientStockTooltip());
});

it('does not warn about insufficient stock for a non storable product', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'update_inventory_internal']);

    $consumable = InventoryHelper::product(['is_storable' => false]);

    $transfer = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$consumable, 7]]);

    expect((float) $transfer->moves->first()->forecast_availability)->toBe(7.0);

    Livewire::test(EditInternal::class, ['record' => $transfer->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('offers the todo action on a draft internal transfer', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'view_inventory_internal']);

    $transfer = internalTransferRecord($this);

    Livewire::test(ViewInternal::class, ['record' => $transfer->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\TodoAction::class);
});

it('offers the check availability action once the internal transfer is confirmed', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'view_inventory_internal']);

    $transfer = internalTransferRecord($this);

    Inventory::confirmTransfer($transfer);

    Livewire::test(ViewInternal::class, ['record' => $transfer->refresh()->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\CheckAvailabilityAction::class);
});

it('cancels an internal transfer through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_inventory_internal', 'view_inventory_internal']);

    $transfer = internalTransferRecord($this);

    Livewire::test(ViewInternal::class, ['record' => $transfer->id])
        ->assertOk()
        ->callAction(OperationActions\CancelAction::class);

    expect($transfer->refresh()->state)->toBe(OperationState::CANCELED);
});

it('lists an internal transfer to a user of the owning company', function () {
    $transfer = internalTransferRecord($this);

    FilamentHelper::actingAsCompanyUser(InventoryHelper::company(), ['view_any_inventory_internal']);

    Livewire::test(ListInternals::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$transfer]);
});

it('hides an internal transfer owned by another company from the list', function () {
    $transfer = internalTransferRecord($this);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), ['view_any_inventory_internal']);

    Livewire::test(ListInternals::class)
        ->assertOk()
        ->assertCanNotSeeTableRecords([$transfer]);
});

it('refuses to open an internal transfer owned by another company', function () {
    $transfer = internalTransferRecord($this);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), [
        'view_any_inventory_internal',
        'view_inventory_internal',
    ]);

    expect(fn () => Livewire::test(ViewInternal::class, ['record' => $transfer->id]))
        ->toThrow(ModelNotFoundException::class);
});
