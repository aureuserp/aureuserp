<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

use Filament\Actions\DeleteAction;
use Livewire\Livewire;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\CreateAssetWarehouse;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\EditAssetWarehouse;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\ListAssetWarehouses;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\ViewAssetWarehouse;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\RelationManagers\WarehouseAssetsRelationManager;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

function createWarehouseEmployee(Company $company, array $attributes = []): Employee
{
    return Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
        ...$attributes,
    ]);
}

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('forbids listing asset warehouses without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListAssetWarehouses::class)->assertForbidden();
});

it('lists, searches, and filters asset warehouses', function () {
    FilamentHelper::actingAs(['view_any_asset_asset::warehouse']);

    $first = AssetWarehouse::factory()->active()->create([
        'name' => 'Central Depot',
        'code' => 'CENTRAL-01',
    ]);

    $second = AssetWarehouse::factory()->inactive()->create([
        'name' => 'Secondary Storage',
        'code' => 'SECONDARY-02',
    ]);

    Livewire::test(ListAssetWarehouses::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$first, $second])
        ->assertCanRenderTableColumn('assets_count')
        ->searchTable('Central Depot')
        ->assertCanSeeTableRecords([$first])
        ->assertCanNotSeeTableRecords([$second])
        ->searchTable(null)
        ->filterTable('is_active', true)
        ->assertCanSeeTableRecords([$first])
        ->assertCanNotSeeTableRecords([$second]);
});

it('creates an asset warehouse with an assigned keeper', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::warehouse',
        'create_asset_asset::warehouse',
    ]);

    $company = Company::query()->firstOrFail();
    $employee = createWarehouseEmployee($company);

    Livewire::test(CreateAssetWarehouse::class)
        ->fillForm([
            'name'                  => 'IT Storage',
            'code'                  => 'WH-IT-01',
            'company_id'            => $company->id,
            'custodian_employee_id' => $employee->id,
            'is_active'             => true,
            'note'                  => 'Main server room storage',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $warehouse = AssetWarehouse::where('code', 'WH-IT-01')->firstOrFail();

    expect($warehouse->name)->toBe('IT Storage')
        ->and($warehouse->custodian_employee_id)->toBe($employee->id)
        ->and($warehouse->is_active)->toBeTrue();
});

it('views, edits, and updates an asset warehouse', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::warehouse',
        'view_asset_asset::warehouse',
        'update_asset_asset::warehouse',
    ]);

    $warehouse = AssetWarehouse::factory()->create(['name' => 'Original Name']);

    Livewire::test(ViewAssetWarehouse::class, ['record' => $warehouse->getRouteKey()])
        ->assertOk()
        ->assertSee('Original Name');

    Livewire::test(EditAssetWarehouse::class, ['record' => $warehouse->getRouteKey()])
        ->assertOk()
        ->fillForm(['name' => 'Updated Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($warehouse->fresh()->name)->toBe('Updated Name');
});

it('renders available stored assets in WarehouseAssetsRelationManager', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::warehouse',
        'view_asset_asset::warehouse',
        'view_any_asset_asset',
        'view_asset_asset',
        'update_asset_asset',
    ]);

    $warehouse = AssetWarehouse::factory()->create();
    $availableAsset = Asset::factory()->company($warehouse->company)->available($warehouse)->create();

    Livewire::test(WarehouseAssetsRelationManager::class, [
        'ownerRecord' => $warehouse,
        'pageClass'   => ViewAssetWarehouse::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$availableAsset]);
});

it('blocks deleting a warehouse that contains stored assets through the table action', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::warehouse',
        'delete_asset_asset::warehouse',
    ]);

    $warehouse = AssetWarehouse::factory()->create();
    Asset::factory()->company($warehouse->company)->available($warehouse)->create();

    Livewire::test(ListAssetWarehouses::class)
        ->callTableAction(DeleteAction::class, $warehouse)
        ->assertNotified();

    expect(AssetWarehouse::withTrashed()->find($warehouse->id))->not->toBeNull()
        ->and(AssetWarehouse::withTrashed()->find($warehouse->id)->trashed())->toBeFalse();
});

it('shows archived asset warehouses under archived preset view and hides them from active views', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::warehouse',
        'view_asset_asset::warehouse',
        'delete_asset_asset::warehouse',
        'restore_asset_asset::warehouse',
    ]);

    $company = Company::query()->firstOrFail();
    $activeWarehouse = AssetWarehouse::factory()->company($company)->active()->create(['name' => 'Active Storage']);
    $archivedWarehouse = AssetWarehouse::factory()->company($company)->active()->create(['name' => 'Old Storage']);

    $archivedWarehouse->delete();
    expect($archivedWarehouse->fresh()->trashed())->toBeTrue();

    Livewire::test(ListAssetWarehouses::class)
        ->assertCanSeeTableRecords([$activeWarehouse])
        ->assertCanNotSeeTableRecords([$archivedWarehouse]);

    Livewire::test(ListAssetWarehouses::class)
        ->call('loadView', 'archived')
        ->assertCanSeeTableRecords([$archivedWarehouse])
        ->assertCanNotSeeTableRecords([$activeWarehouse]);
});
