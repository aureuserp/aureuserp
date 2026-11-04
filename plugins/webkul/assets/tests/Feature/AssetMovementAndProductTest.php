<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

use Livewire\Livewire;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Filament\Resources\AssetMovementResource;
use Webkul\Asset\Filament\Resources\AssetMovementResource\Pages\ListAssetMovements;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\ListAssets;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\EditProduct;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\ListProducts;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Asset\Filament\Resources\ProductResource\RelationManagers\ProductAssetsRelationManager;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('rejects transfer to the same holder with a clear validation error', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);

    $asset = Asset::factory()->company($company)->heldByEmployee($employee)->create();

    Livewire::test(ListAssets::class)
        ->callTableAction('transfer', $asset, data: [
            'employee_id'  => $employee->id,
            'condition_in' => AssetCondition::Good->value,
            'issued_at'    => now(),
        ])
        ->assertHasTableActionErrors(['employee_id']);
});

it('enforces damage attachments on damaged return when setting is enabled', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $settings = app(AssetSettings::class);
    $settings->require_attachments_on_damage = true;
    $settings->save();

    $company = Company::query()->firstOrFail();
    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $asset = Asset::factory()->company($company)->heldByEmployee($employee)->create();

    Livewire::test(ListAssets::class)
        ->callTableAction('return', $asset, data: [
            'condition_in'       => AssetCondition::Damaged->value,
            'ended_at'           => now(),
            'warehouse_id'       => $warehouse->id,
            'return_note'        => 'Broken hardware',
            'return_attachments' => [],
        ])
        ->assertHasTableActionErrors(['return_attachments']);
});

it('renders asset movements list and filters correctly', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $asset = Asset::factory()->company($company)->available()->create();

    $movement = AssetMovement::factory()->issue()->create([
        'asset_id'    => $asset->id,
        'company_id'  => $company->id,
        'occurred_at' => now(),
    ]);

    Livewire::test(ListAssetMovements::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$movement]);
});

it('creates, edits, lists, and views asset products with assets relation manager', function () {
    FilamentHelper::actingAs([
        'view_any_asset_product',
        'create_asset_product',
        'update_asset_product',
        'view_asset_product',
    ]);

    $company = Company::query()->firstOrFail();
    $category = AssetCategory::factory()->create(['name' => 'IT Equipment']);

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'company_id'  => $company->id,
            'category_id' => $category->id,
            'name'        => 'Lenovo ThinkCentre M70q',
            'model'       => 'M70q Tiny',
            'cost'        => 3200.00,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $product = AssetProduct::query()->where('name', 'Lenovo ThinkCentre M70q')->firstOrFail();
    expect($product->model)->toBe('M70q Tiny')
        ->and((float) $product->cost)->toBe(3200.00)
        ->and($product->category_id)->toBe($category->id);

    Livewire::test(ListProducts::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$product]);

    Livewire::test(EditProduct::class, ['record' => $product->id])
        ->fillForm([
            'model' => 'M70q Gen 4',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->fresh()->model)->toBe('M70q Gen 4');

    $asset = Asset::factory()->company($company)->available()->forProduct($product)->create();

    Livewire::test(ProductAssetsRelationManager::class, [
        'ownerRecord' => $product,
        'pageClass'   => ViewProduct::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$asset]);
});

it('verifies navigation groups and movement table preset views', function () {
    expect(AssetMovementResource::getNavigationGroup())->toBe(NavigationGroup::Assets);
    expect(ProductResource::getNavigationGroup())->toBe(NavigationGroup::Assets);

    FilamentHelper::actingAs([
        'view_any_asset_asset',
    ]);

    $livewire = Livewire::test(ListAssetMovements::class);
    $views = $livewire->instance()->getPresetTableViews();

    expect($views)->toHaveKeys(['all', 'assets', 'consumables', 'issues', 'returns', 'transfers', 'scrapped_lost', 'damaged']);

    foreach (array_keys($views) as $viewKey) {
        $livewire->set('activeTab', $viewKey)->assertOk();
    }
});

it('separates asset and consumable movements into dedicated preset views', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();

    $assetProduct = AssetProduct::factory()->create([
        'company_id' => $company->id,
        'type'       => AssetProductType::Asset,
    ]);
    $consumableProduct = AssetProduct::factory()->create([
        'company_id' => $company->id,
        'type'       => AssetProductType::Consumable,
    ]);

    $asset = Asset::factory()->forProduct($assetProduct)->company($company)->available()->create();
    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $consumableProduct->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    $assetMovement = $asset->movements()->firstOrFail();
    $consumableMovement = $batch->movements()->firstOrFail();

    Livewire::test(ListAssetMovements::class)
        ->call('loadView', 'assets')
        ->assertCanSeeTableRecords([$assetMovement])
        ->assertCanNotSeeTableRecords([$consumableMovement]);

    Livewire::test(ListAssetMovements::class)
        ->call('loadView', 'consumables')
        ->assertCanSeeTableRecords([$consumableMovement])
        ->assertCanNotSeeTableRecords([$assetMovement]);
});

it('shows archived products under archived_products preset view', function () {
    FilamentHelper::actingAs([
        'view_any_asset_product',
        'view_asset_product',
        'delete_asset_product',
        'restore_asset_product',
    ]);

    $company = Company::query()->firstOrFail();
    $activeProduct = AssetProduct::factory()->create([
        'company_id' => $company->id,
        'is_active'  => true,
    ]);
    $archivedProduct = AssetProduct::factory()->create([
        'company_id' => $company->id,
        'is_active'  => true,
    ]);

    $archivedProduct->delete();
    expect($archivedProduct->fresh()->trashed())->toBeTrue();

    Livewire::test(ListProducts::class)
        ->assertCanSeeTableRecords([$activeProduct])
        ->assertCanNotSeeTableRecords([$archivedProduct]);

    Livewire::test(ListProducts::class)
        ->call('loadView', 'archived_products')
        ->assertCanSeeTableRecords([$archivedProduct])
        ->assertCanNotSeeTableRecords([$activeProduct]);
});

it('displays asset tag and product name in asset movements even when asset is archived', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->create([
        'name'       => 'Archived Model Dell XPS',
        'company_id' => $company->id,
    ]);
    $asset = Asset::factory()->available()->forProduct($product)->create([
        'company_id' => $company->id,
        'asset_tag'  => 'AST-ARCHIVED-001',
    ]);

    $movement = AssetMovement::query()->where('asset_id', $asset->id)->firstOrFail();

    $asset->delete();
    expect($asset->fresh()->trashed())->toBeTrue();

    expect($movement->fresh()->asset)->not->toBeNull()
        ->and($movement->fresh()->asset->asset_tag)->toBe('AST-ARCHIVED-001');

    Livewire::test(ListAssetMovements::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$movement]);
});

it('hides editOptionAction on category_id when empty and shows it when selected', function () {
    FilamentHelper::actingAs([
        'view_any_asset_product',
        'create_asset_product',
        'update_asset_product',
    ]);

    $category = AssetCategory::factory()->create();

    $livewire = Livewire::test(CreateProduct::class);
    $component = $livewire->instance()->getSchema('form')->getFlatComponents()['category_id'];
    $action = $component->getAction('editOption');

    expect($action->isVisible())->toBeFalse();

    $livewire->fillForm(['category_id' => $category->id]);
    $component = $livewire->instance()->getSchema('form')->getFlatComponents()['category_id'];
    $action = $component->getAction('editOption');

    expect($action->isVisible())->toBeTrue();
});

it('exposes product type field, filter, and quantity aware counters', function () {
    FilamentHelper::actingAs([
        'view_any_asset_product',
        'create_asset_product',
    ]);

    $livewire = Livewire::test(CreateProduct::class);
    $components = $livewire->instance()->getSchema('form')->getFlatComponents();

    expect($components)->toHaveKey('type');

    $list = Livewire::test(ListProducts::class);

    expect($list->instance()->getTable()->getFilters())->toHaveKey('type');

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();

    $assetProduct = AssetProduct::factory()->create([
        'company_id' => $company->id,
        'type'       => AssetProductType::Asset,
    ]);

    $consumableProduct = AssetProduct::factory()->create([
        'company_id' => $company->id,
        'type'       => AssetProductType::Consumable,
    ]);

    Asset::factory()->company($company)->available()->forProduct($assetProduct)->create(['warehouse_id' => $warehouse->id]);
    Asset::factory()->company($company)->available()->forProduct($assetProduct)->create(['warehouse_id' => $warehouse->id]);

    Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $consumableProduct->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 50,
    ]);

    $list->assertTableColumnStateSet('assets_count', 2, $assetProduct)
        ->assertTableColumnStateSet('assets_count', 50, $consumableProduct)
        ->assertTableColumnStateSet('available_assets_count', 2, $assetProduct)
        ->assertTableColumnStateSet('available_assets_count', 50, $consumableProduct);
});
