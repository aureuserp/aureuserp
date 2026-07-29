<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Filament\Clusters\Operations\Actions as OperationActions;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\CreateDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\EditDropship;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\ListDropships;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource\Pages\ViewDropship;
use Webkul\Inventory\Settings\LogisticSettings;
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

    $settings = app(LogisticSettings::class);
    $settings->enable_dropshipping = true;
    $settings->save();

    $this->product = InventoryHelper::product();
});

it('forbids listing dropships without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListDropships::class)->assertForbidden();
});

it('lists dropships for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship']);

    Livewire::test(ListDropships::class)->assertOk();
});

it('lists a dropship with its key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Livewire::test(ListDropships::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$dropship])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state')
        ->assertCanRenderTableColumn('company.name')
        ->assertTableColumnExists('sourceLocation.full_name')
        ->assertTableColumnExists('destinationLocation.full_name');
});

it('finds a dropship by searching its reference', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Livewire::test(ListDropships::class)
        ->searchTable($dropship->name)
        ->assertCanSeeTableRecords([$dropship]);
});

it('renders the dropship create page', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship', 'create_inventory_dropship']);

    Livewire::test(CreateDropship::class)->assertOk();
});

it('renders the dropship edit page', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship', 'update_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Livewire::test(EditDropship::class, ['record' => $dropship->id])->assertOk();
});

it('does not warn about insufficient stock on a dropship because it ships from the supplier', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship', 'update_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Livewire::test(EditDropship::class, ['record' => $dropship->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('offers the todo action on a draft dropship', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship', 'view_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Livewire::test(ViewDropship::class, ['record' => $dropship->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\TodoAction::class);
});

it('hides the todo action once the dropship is assigned on confirm', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship', 'view_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Inventory::confirmTransfer($dropship);

    expect($dropship->refresh()->state)->toBe(OperationState::ASSIGNED);

    Livewire::test(ViewDropship::class, ['record' => $dropship->id])
        ->assertOk()
        ->assertActionHidden(OperationActions\TodoAction::class)
        ->assertActionVisible(OperationActions\ValidateAction::class);
});

it('hides the validate and cancel actions on a done dropship', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship', 'view_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Inventory::confirmTransfer($dropship);
    Inventory::completeTransfer($dropship->refresh());

    expect($dropship->refresh()->state)->toBe(OperationState::DONE);

    Livewire::test(ViewDropship::class, ['record' => $dropship->id])
        ->assertOk()
        ->assertActionHidden(OperationActions\ValidateAction::class)
        ->assertActionHidden(OperationActions\CancelAction::class);
});

it('cancels a dropship through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_inventory_dropship', 'view_inventory_dropship']);

    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    Livewire::test(ViewDropship::class, ['record' => $dropship->id])
        ->assertOk()
        ->callAction(OperationActions\CancelAction::class);

    expect($dropship->refresh()->state)->toBe(OperationState::CANCELED);
});

it('lists a dropship to a user of the owning company', function () {
    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    FilamentHelper::actingAsCompanyUser(InventoryHelper::company(), ['view_any_inventory_dropship']);

    Livewire::test(ListDropships::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$dropship]);
});

it('hides a dropship owned by another company from the list', function () {
    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), ['view_any_inventory_dropship']);

    Livewire::test(ListDropships::class)
        ->assertOk()
        ->assertCanNotSeeTableRecords([$dropship]);
});

it('refuses to open a dropship owned by another company', function () {
    $dropship = InventoryHelper::dropship([[$this->product, 10]]);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), [
        'view_any_inventory_dropship',
        'view_inventory_dropship',
    ]);

    expect(fn () => Livewire::test(ViewDropship::class, ['record' => $dropship->id]))
        ->toThrow(ModelNotFoundException::class);
});
