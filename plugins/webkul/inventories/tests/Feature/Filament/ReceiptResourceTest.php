<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Filament\Clusters\Operations\Actions as OperationActions;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\CreateReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\EditReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ListReceipts;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ViewReceipt;
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
});

it('forbids listing receipts without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListReceipts::class)->assertForbidden();
});

it('lists receipts for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt']);

    Livewire::test(ListReceipts::class)->assertOk();
});

it('renders the receipt create page', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'create_inventory_receipt']);

    Livewire::test(CreateReceipt::class)->assertOk();
});

it('lists a receipt with its key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    Livewire::test(ListReceipts::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$receipt])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state')
        ->assertCanRenderTableColumn('scheduled_at')
        ->assertCanRenderTableColumn('company.name')
        ->assertTableColumnExists('operationType.name');
});

it('finds a receipt by searching its reference', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    Livewire::test(ListReceipts::class)
        ->searchTable($receipt->name)
        ->assertCanSeeTableRecords([$receipt]);
});

it('renders the receipt edit page', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'update_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    Livewire::test(EditReceipt::class, ['record' => $receipt->id])->assertOk();
});

it('never warns about insufficient stock on a receipt because an incoming move adds stock', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'update_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    Livewire::test(EditReceipt::class, ['record' => $receipt->id])
        ->assertOk()
        ->assertDontSee(InventoryHelper::insufficientStockTooltip());
});

it('offers the todo action on a draft receipt', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'view_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    expect($receipt->state)->toBe(OperationState::DRAFT);

    Livewire::test(ViewReceipt::class, ['record' => $receipt->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\TodoAction::class);
});

it('hides the todo action once the receipt leaves draft', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'view_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    Inventory::confirmTransfer($receipt);

    Livewire::test(ViewReceipt::class, ['record' => $receipt->refresh()->id])
        ->assertOk()
        ->assertActionHidden(OperationActions\TodoAction::class);
});

it('offers the return action only once the receipt is done', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'view_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $product = InventoryHelper::product();
    $receipt = InventoryHelper::receipt($warehouse, [[$product, 5]]);

    Livewire::test(ViewReceipt::class, ['record' => $receipt->id])
        ->assertOk()
        ->assertActionHidden(OperationActions\ReturnAction::class);

    Inventory::confirmTransfer($receipt);
    InventoryHelper::pick($receipt->refresh()->moves->first(), 5);
    Inventory::completeTransfer($receipt->refresh());

    expect($receipt->refresh()->state)->toBe(OperationState::DONE);

    Livewire::test(ViewReceipt::class, ['record' => $receipt->id])
        ->assertOk()
        ->assertActionVisible(OperationActions\ReturnAction::class);
});

it('hides the validate and cancel actions on a done receipt', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'view_inventory_receipt']);

    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    Inventory::confirmTransfer($receipt);
    InventoryHelper::pick($receipt->refresh()->moves->first(), 5);
    Inventory::completeTransfer($receipt->refresh());

    Livewire::test(ViewReceipt::class, ['record' => $receipt->id])
        ->assertOk()
        ->assertActionHidden(OperationActions\ValidateAction::class)
        ->assertActionHidden(OperationActions\CancelAction::class);
});

it('lists a receipt to a user of the owning company', function () {
    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    FilamentHelper::actingAsCompanyUser(InventoryHelper::company(), ['view_any_inventory_receipt']);

    Livewire::test(ListReceipts::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$receipt]);
});

it('hides a receipt owned by another company from the list', function () {
    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), ['view_any_inventory_receipt']);

    Livewire::test(ListReceipts::class)
        ->assertOk()
        ->assertCanNotSeeTableRecords([$receipt]);
});

it('refuses to open a receipt owned by another company', function () {
    $warehouse = InventoryHelper::warehouse();
    $receipt = InventoryHelper::receipt($warehouse, [[InventoryHelper::product(), 5]]);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), [
        'view_any_inventory_receipt',
        'view_inventory_receipt',
    ]);

    expect(fn () => Livewire::test(ViewReceipt::class, ['record' => $receipt->id]))
        ->toThrow(ModelNotFoundException::class);
});
