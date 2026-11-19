<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\View\ViewManager;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Pagination\Paginator;
use Livewire\Livewire;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Filament\Exports\AssetCategoryExporter;
use Webkul\Asset\Filament\Exports\AssetExporter;
use Webkul\Asset\Filament\Exports\AssetMovementExporter;
use Webkul\Asset\Filament\Exports\AssetProductExporter;
use Webkul\Asset\Filament\Exports\AssetSiteExporter;
use Webkul\Asset\Filament\Exports\AssetWarehouseExporter;
use Webkul\Asset\Filament\Resources\AssetMovementResource\Pages\ListAssetMovements;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\ListAssets;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\ViewAsset;
use Webkul\Asset\Filament\Resources\AssetResource\RelationManagers\AssignmentsRelationManager;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\ListAssetSites;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\ViewAssetSite;
use Webkul\Asset\Filament\Resources\AssetSiteResource\RelationManagers\SiteAssetsRelationManager;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\ListAssetWarehouses;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\ViewAssetWarehouse;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\RelationManagers\WarehouseAssetsRelationManager;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\ListProducts;
use Webkul\Asset\Filament\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Asset\Filament\Resources\ProductResource\RelationManagers\ProductAssetsRelationManager;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('ensures zero summarizers across all asset and movement tables', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_any_asset_product',
        'view_any_asset_asset::warehouse',
        'view_any_asset_asset::site',
    ]);

    $tables = [
        Livewire::test(ListAssets::class)->instance()->getTable(),
        Livewire::test(ListProducts::class)->instance()->getTable(),
        Livewire::test(ListAssetWarehouses::class)->instance()->getTable(),
        Livewire::test(ListAssetSites::class)->instance()->getTable(),
        Livewire::test(ListAssetMovements::class)->instance()->getTable(),
    ];

    foreach ($tables as $table) {
        foreach ($table->getColumns() as $column) {
            if ($column->getName() === 'quantity') {
                continue;
            }

            expect($column->getSummarizers())->toBeEmpty();
        }
    }
});

it('exposes movement quantity and product type ledger columns and exporter fields', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $table = Livewire::test(ListAssetMovements::class)->instance()->getTable();

    $columns = collect($table->getColumns())->keyBy(fn ($column): string => $column->getName());

    expect($columns->has('quantity'))->toBeTrue()
        ->and($columns['quantity']->getSummarizers())->not->toBeEmpty()
        ->and($table->getFilters())->toHaveKey('product_type');

    $movementColumns = collect(AssetMovementExporter::getColumns())->keyBy(fn ($column): string => $column->getName());
    expect($movementColumns->has('quantity'))->toBeTrue();

    $assetColumns = collect(AssetExporter::getColumns())->keyBy(fn ($column): string => $column->getName());
    expect($assetColumns->has('product.type'))->toBeTrue()
        ->and($assetColumns->has('initial_quantity'))->toBeTrue()
        ->and($assetColumns->has('quantity'))->toBeTrue();

    $productColumns = collect(AssetProductExporter::getColumns())->keyBy(fn ($column): string => $column->getName());
    expect($productColumns->has('type'))->toBeTrue();
});

it('calculates product distribution status counts and cost valuation', function () {
    FilamentHelper::actingAs(['view_any_asset_product']);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();
    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);

    $product = AssetProduct::factory()->company($company)->create([
        'name' => 'Dell Latitude 5540',
        'cost' => 1000.00,
    ]);

    Asset::factory()->count(2)->company($company)->forProduct($product)->available($warehouse)->create([
        'cost' => 1000.00,
    ]);

    Asset::factory()->count(2)->company($company)->forProduct($product)->heldByEmployee($employee)->create([
        'cost' => 1000.00,
    ]);

    Asset::factory()->company($company)->forProduct($product)->create([
        'status'              => AssetStatus::Scrapped,
        'warehouse_id'        => null,
        'current_employee_id' => null,
        'current_site_id'     => null,
        'cost'                => 1000.00,
    ]);

    Livewire::test(ListProducts::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$product])
        ->assertTableColumnStateSet('assets_count', 5, record: $product)
        ->assertTableColumnStateSet('available_assets_count', 2, record: $product)
        ->assertTableColumnStateSet('held_assets_count', 2, record: $product)
        ->assertTableColumnStateSet('terminated_assets_count', 1, record: $product)
        ->assertTableColumnStateSet('assets_sum_cost', 5000.00, record: $product);
});

it('accurately counts and sums costs for warehouse available assets', function () {
    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->company($company)->create(['cost' => 1500.00]);

    $warehouseA = AssetWarehouse::factory()->company($company)->active()->create(['name' => 'Depot Alpha']);
    $warehouseB = AssetWarehouse::factory()->company($company)->active()->create(['name' => 'Depot Beta']);

    Asset::factory()->count(2)->company($company)->forProduct($product)->available($warehouseA)->create([
        'cost' => 1500.00,
    ]);
    Asset::factory()->count(1)->company($company)->forProduct($product)->available($warehouseB)->create([
        'cost' => 800.00,
    ]);

    FilamentHelper::actingAs(['view_any_asset_asset::warehouse']);

    Livewire::test(ListAssetWarehouses::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$warehouseA, $warehouseB])
        ->assertTableColumnStateSet('assets_count', 2, record: $warehouseA)
        ->assertTableColumnStateSet('assets_sum_cost', 3000.00, record: $warehouseA)
        ->assertTableColumnStateSet('assets_count', 1, record: $warehouseB)
        ->assertTableColumnStateSet('assets_sum_cost', 800.00, record: $warehouseB);
});

it('accurately counts and sums costs for site held assets', function () {
    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->company($company)->create(['cost' => 2000.00]);

    $siteType = AssetSiteType::whereNull('company_id')->first() ?? AssetSiteType::factory()->create();
    $site = AssetSite::factory()->company($company)->active()->custodyZone()->create([
        'name'         => 'Construction Site A',
        'site_type_id' => $siteType->id,
    ]);

    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);

    Asset::factory()->count(2)->company($company)->forProduct($product)->create([
        'status'              => AssetStatus::Held,
        'warehouse_id'        => null,
        'current_employee_id' => $employee->id,
        'current_site_id'     => $site->id,
        'cost'                => 2000.00,
    ]);

    FilamentHelper::actingAs(['view_any_asset_asset::site']);

    Livewire::test(ListAssetSites::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$site])
        ->assertTableColumnStateSet('assets_count', 2, record: $site)
        ->assertTableColumnStateSet('assets_sum_cost', 4000.00, record: $site);
});

it('filters movement ledger by hierarchical category and department', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();

    $parentCategory = AssetCategory::factory()->create(['name' => 'IT Hardware']);
    $childCategory = AssetCategory::factory()->create([
        'name'      => 'Laptops',
        'parent_id' => $parentCategory->id,
    ]);
    $otherCategory = AssetCategory::factory()->create(['name' => 'Vehicles']);

    $productLaptop = AssetProduct::factory()->company($company)->create([
        'name'        => 'ThinkPad X1',
        'category_id' => $childCategory->id,
    ]);
    $productTruck = AssetProduct::factory()->company($company)->create([
        'name'        => 'Delivery Truck',
        'category_id' => $otherCategory->id,
    ]);

    $deptEngineering = Department::factory()->create(['company_id' => $company->id, 'name' => 'Engineering']);
    $deptFinance = Department::factory()->create(['company_id' => $company->id, 'name' => 'Finance']);

    $engineer = Employee::factory()->create([
        'company_id'    => $company->id,
        'department_id' => $deptEngineering->id,
        'user_id'       => User::factory()->create()->id,
        'is_active'     => true,
    ]);
    $accountant = Employee::factory()->create([
        'company_id'    => $company->id,
        'department_id' => $deptFinance->id,
        'user_id'       => User::factory()->create()->id,
        'is_active'     => true,
    ]);

    $custodyService = app(AssetCustodyService::class);
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();

    $laptopAsset = Asset::factory()->company($company)->forProduct($productLaptop)->available($warehouse)->create();
    $custodyService->issue($laptopAsset, [
        'employee_id'   => $engineer->id,
        'condition_out' => AssetCondition::New,
        'issued_at'     => now()->subDays(2),
    ]);

    $truckAsset = Asset::factory()->company($company)->forProduct($productTruck)->available($warehouse)->create();
    $custodyService->issue($truckAsset, [
        'employee_id'   => $accountant->id,
        'condition_out' => AssetCondition::New,
        'issued_at'     => now()->subDays(2),
    ]);

    $laptopIssueMovement = AssetMovement::where('asset_id', $laptopAsset->id)->where('to_employee_id', $engineer->id)->firstOrFail();
    $truckIssueMovement = AssetMovement::where('asset_id', $truckAsset->id)->where('to_employee_id', $accountant->id)->firstOrFail();

    Livewire::test(ListAssetMovements::class)
        ->assertOk()
        ->filterTable('category_id', $parentCategory->id)
        ->assertCanSeeTableRecords([$laptopIssueMovement])
        ->assertCanNotSeeTableRecords([$truckIssueMovement]);

    Livewire::test(ListAssetMovements::class)
        ->assertOk()
        ->filterTable('department_id', $deptEngineering->id)
        ->assertCanSeeTableRecords([$laptopIssueMovement])
        ->assertCanNotSeeTableRecords([$truckIssueMovement]);

    $custodyService->transfer($laptopAsset, [
        'employee_id'  => $accountant->id,
        'condition_in' => AssetCondition::Good,
        'issued_at'    => now()->subDay(),
    ]);

    $transferMovement = AssetMovement::where('asset_id', $laptopAsset->id)
        ->where('from_employee_id', $engineer->id)
        ->where('to_employee_id', $accountant->id)
        ->firstOrFail();

    Livewire::test(ListAssetMovements::class)
        ->assertOk()
        ->filterTable('department_id', $deptEngineering->id)
        ->assertCanSeeTableRecords([$transferMovement]);

    Livewire::test(ListAssetMovements::class)
        ->assertOk()
        ->filterTable('department_id', $deptFinance->id)
        ->assertCanSeeTableRecords([$transferMovement]);
});

it('filters movement export queries using modal options date range and movement types', function () {
    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->company($company)->create();
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();
    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);

    $asset = Asset::factory()->company($company)->forProduct($product)->available($warehouse)->create();
    $custodyService = app(AssetCustodyService::class);

    $registeredMovement = AssetMovement::where('asset_id', $asset->id)->firstOrFail();
    AssetMovement::whereKey($registeredMovement->id)->update(['occurred_at' => now()->subDays(5)]);

    $custodyService->issue($asset, [
        'employee_id'   => $employee->id,
        'condition_out' => AssetCondition::New,
        'issued_at'     => now()->subDays(3),
    ]);
    $issuedMovement = AssetMovement::where('asset_id', $asset->id)->where('type', MovementType::Issued)->firstOrFail();

    $custodyService->return($asset, [
        'warehouse_id' => $warehouse->id,
        'condition_in' => AssetCondition::Good,
        'returned_at'  => now()->subDay(),
    ]);
    $returnedMovement = AssetMovement::where('asset_id', $asset->id)->where('type', MovementType::Returned)->firstOrFail();

    $filteredByDate = AssetMovementExporter::applyExportOptions(AssetMovement::query()->where('asset_id', $asset->id), [
        'date_from'  => now()->subDays(4)->toDateString(),
        'date_until' => now()->subDays(2)->toDateString(),
    ])->get();

    expect($filteredByDate->pluck('id')->all())->toBe([$issuedMovement->id]);

    $filteredByTypes = AssetMovementExporter::applyExportOptions(AssetMovement::query()->where('asset_id', $asset->id), [
        'types' => [MovementType::Issued->value, MovementType::Returned->value],
    ])->get();

    expect($filteredByTypes->pluck('id')->all())
        ->toContain($issuedMovement->id, $returnedMovement->id)
        ->not->toContain($registeredMovement->id);

    $filteredCombined = AssetMovementExporter::applyExportOptions(AssetMovement::query()->where('asset_id', $asset->id), [
        'date_from' => now()->subDays(2)->toDateString(),
        'types'     => [MovementType::Returned->value],
    ])->get();

    expect($filteredCombined->pluck('id')->all())->toBe([$returnedMovement->id]);

    $parentSite = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'HQ Zone']);
    $childSite = AssetSite::factory()->company($company)->withParent($parentSite)->create(['name' => 'HQ Floor 3']);
    $otherEmployee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);

    $siteIssueMovement = $custodyService->issue($asset, [
        'employee_id'   => $otherEmployee->id,
        'site_id'       => $childSite->id,
        'condition_out' => AssetCondition::Good,
        'issued_at'     => now(),
    ]);

    $filteredBySiteSubtree = AssetMovementExporter::applyExportOptions(AssetMovement::query()->where('asset_id', $asset->id), [
        'site_ids' => [$parentSite->id],
    ])->get();

    expect($filteredBySiteSubtree->pluck('id')->all())->toBe([$siteIssueMovement->id]);

    $filteredByEmployee = AssetMovementExporter::applyExportOptions(AssetMovement::query()->where('asset_id', $asset->id), [
        'employee_ids' => [$otherEmployee->id],
    ])->get();

    expect($filteredByEmployee->pluck('id')->all())->toBe([$siteIssueMovement->id]);

    $optionsComponents = AssetMovementExporter::getOptionsFormComponents();
    expect($optionsComponents)->not->toBeEmpty();

    $movementFields = collect($optionsComponents[0]->getDefaultChildComponents())
        ->keyBy(fn ($component): string => $component->getName());

    expect($movementFields->has('site_ids'))->toBeTrue()
        ->and($movementFields->has('employee_ids'))->toBeTrue()
        ->and($movementFields['site_ids']->isDehydrated())->toBeTrue()
        ->and($movementFields['employee_ids']->isDehydrated())->toBeTrue()
        ->and($movementFields['employee_ids']->isPreloaded())->toBeFalse()
        ->and($movementFields['employee_ids']->isSearchable())->toBeTrue();
});

it('resolves exporter columns without missing translations and wires toolbar export actions', function () {
    $assetColumns = AssetExporter::getColumns();
    expect($assetColumns)->not->toBeEmpty();

    foreach ($assetColumns as $column) {
        $label = $column->getLabel();
        expect($label)
            ->toBeString()
            ->not->toBeEmpty()
            ->not->toStartWith('assets::');
    }

    $export = new Export(['successful_rows' => 42]);
    $assetNotification = AssetExporter::getCompletedNotificationBody($export);
    expect($assetNotification)
        ->toBeString()
        ->not->toBeEmpty()
        ->not->toStartWith('assets::')
        ->toContain('42');

    $movementColumns = AssetMovementExporter::getColumns();
    expect($movementColumns)->not->toBeEmpty();

    foreach ($movementColumns as $column) {
        $label = $column->getLabel();
        expect($label)
            ->toBeString()
            ->not->toBeEmpty()
            ->not->toStartWith('assets::');
    }

    $movementNotification = AssetMovementExporter::getCompletedNotificationBody($export);
    expect($movementNotification)
        ->toBeString()
        ->not->toBeEmpty()
        ->not->toStartWith('assets::')
        ->toContain('42');

    FilamentHelper::actingAs(['view_any_asset_asset']);

    $assetsLivewire = Livewire::test(ListAssets::class)->assertOk();
    $assetToolbarActions = $assetsLivewire->instance()->getTable()->getToolbarActions();
    $hasAssetExport = collect($assetToolbarActions)->contains(function ($action) {
        return $action instanceof ExportAction || (method_exists($action, 'getName') && $action->getName() === 'export');
    });
    expect($hasAssetExport)->toBeTrue();

    $movementLivewire = Livewire::test(ListAssetMovements::class)->assertOk();
    $movementToolbarActions = $movementLivewire->instance()->getTable()->getToolbarActions();
    $hasMovementExport = collect($movementToolbarActions)->contains(function ($action) {
        return $action instanceof ExportAction || (method_exists($action, 'getName') && $action->getName() === 'export');
    });
    expect($hasMovementExport)->toBeTrue();
});

it('wires direct export action on all four custody relation managers', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_any_asset_product',
        'view_any_asset_asset::warehouse',
        'view_any_asset_asset::site',
        'view_asset_product',
        'view_asset_asset::warehouse',
        'view_asset_asset::site',
        'view_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->company($company)->create();
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();
    $site = AssetSite::factory()->company($company)->active()->create();
    $asset = Asset::factory()->company($company)->forProduct($product)->available($warehouse)->create();

    $warehouseRM = Livewire::test(WarehouseAssetsRelationManager::class, [
        'ownerRecord' => $warehouse,
        'pageClass'   => ViewAssetWarehouse::class,
    ])->assertOk();
    $hasWarehouseExport = collect($warehouseRM->instance()->getTable()->getToolbarActions())->contains(function ($action) {
        return $action instanceof ExportAction || (method_exists($action, 'getName') && $action->getName() === 'export');
    });
    expect($hasWarehouseExport)->toBeTrue();

    $siteRM = Livewire::test(SiteAssetsRelationManager::class, [
        'ownerRecord' => $site,
        'pageClass'   => ViewAssetSite::class,
    ])->assertOk();
    $hasSiteExport = collect($siteRM->instance()->getTable()->getToolbarActions())->contains(function ($action) {
        return $action instanceof ExportAction || (method_exists($action, 'getName') && $action->getName() === 'export');
    });
    expect($hasSiteExport)->toBeTrue();

    $productRM = Livewire::test(ProductAssetsRelationManager::class, [
        'ownerRecord' => $product,
        'pageClass'   => ViewProduct::class,
    ])->assertOk();
    $hasProductExport = collect($productRM->instance()->getTable()->getToolbarActions())->contains(function ($action) {
        return $action instanceof ExportAction || (method_exists($action, 'getName') && $action->getName() === 'export');
    });
    expect($hasProductExport)->toBeTrue();

    $assignmentsRM = Livewire::test(AssignmentsRelationManager::class, [
        'ownerRecord' => $asset,
        'pageClass'   => ViewAsset::class,
    ])->assertOk();
    $hasAssignmentsExport = collect($assignmentsRM->instance()->getTable()->getToolbarActions())->contains(function ($action) {
        return $action instanceof ExportAction || (method_exists($action, 'getName') && $action->getName() === 'export');
    });
    expect($hasAssignmentsExport)->toBeTrue();
});

it('filters movement ledger by site using recursive subtree', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();
    $parentSite = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'HQ Complex']);
    $childSite = AssetSite::factory()->company($company)->withParent($parentSite)->create(['name' => 'HQ Floor 1']);
    $otherSite = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Dammam Site']);

    $custodyService = app(AssetCustodyService::class);
    $asset1 = Asset::factory()->company($company)->available()->create();
    $asset2 = Asset::factory()->company($company)->available()->create();

    $mov1 = $custodyService->issue($asset1, [
        'allocation_mode' => 'site_asset',
        'site_id'         => $childSite->id,
        'issued_at'       => now()->subDay(),
    ]);

    $mov2 = $custodyService->issue($asset2, [
        'allocation_mode' => 'site_asset',
        'site_id'         => $otherSite->id,
        'issued_at'       => now()->subDay(),
    ]);

    Livewire::test(ListAssetMovements::class)
        ->assertOk()
        ->filterTable('site_id', $parentSite->id)
        ->assertCanSeeTableRecords([$mov1])
        ->assertCanNotSeeTableRecords([$mov2]);
});

it('includes currentSite.full_name in AssetExporter columns', function () {
    $columns = collect(AssetExporter::getColumns())->keyBy(fn ($col) => $col->getName());

    expect($columns->has('currentSite.full_name'))->toBeTrue()
        ->and($columns->has('currentSite.name'))->toBeFalse();
});

it('applies multi-select export options on AssetExporter', function () {
    $company = Company::query()->firstOrFail();
    $cat = AssetCategory::factory()->company($company)->create();
    $prod = AssetProduct::factory()->company($company)->create(['category_id' => $cat->id]);
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();
    $site = AssetSite::factory()->company($company)->assetLocation()->create();
    $user = User::factory()->create();
    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => $user->id,
        'is_active'  => true,
    ]);

    $matchingAsset = Asset::factory()->company($company)->forProduct($prod)->heldByEmployee($employee)->create([
        'status'       => AssetStatus::Held,
        'condition'    => AssetCondition::Good,
        'warehouse_id' => null,
    ]);

    $otherAsset = Asset::factory()->company($company)->available()->create([
        'status'    => AssetStatus::Available,
        'condition' => AssetCondition::New,
    ]);

    $filtered = AssetExporter::applyExportOptions(Asset::query(), [
        'product_ids'   => [$prod->id],
        'statuses'      => [AssetStatus::Held->value],
        'conditions'    => [AssetCondition::Good->value],
        'category_ids'  => [$cat->id],
        'employee_ids'  => [$employee->id],
    ]);

    expect($filtered->pluck('id')->all())->toBe([$matchingAsset->id]);
});

it('verifies calculations and column configurations of all new table exporters', function () {
    $company = Company::query()->firstOrFail();
    $cat = AssetCategory::factory()->company($company)->create(['name' => 'Network Hardware']);
    $prod = AssetProduct::factory()->company($company)->create(['category_id' => $cat->id, 'cost' => 500.00]);
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create(['name' => 'Central Hub']);
    $site = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Branch Office']);

    Asset::factory()->company($company)->forProduct($prod)->available($warehouse)->create(['cost' => 500.00]);
    Asset::factory()->company($company)->forProduct($prod)->heldBySite($site)->create(['cost' => 500.00]);

    // Product Exporter
    $prodColumns = collect(AssetProductExporter::getColumns())->keyBy(fn ($c) => $c->getName());
    expect($prodColumns->has('available_assets_count'))->toBeTrue()
        ->and($prodColumns->has('held_assets_count'))->toBeTrue()
        ->and($prodColumns->has('assets_sum_cost'))->toBeTrue();

    $prodRow = AssetProductExporter::modifyQuery(AssetProduct::query()->whereKey($prod->id))->first();
    expect((float) $prodRow->assets_count)->toBe(2.0)
        ->and((float) $prodRow->available_assets_count)->toBe(1.0)
        ->and((float) $prodRow->held_assets_count)->toBe(1.0)
        ->and((float) $prodRow->assets_sum_cost)->toBe(1000.00);

    // Warehouse Exporter
    $whColumns = collect(AssetWarehouseExporter::getColumns())->keyBy(fn ($c) => $c->getName());
    expect($whColumns->has('assets_count'))->toBeTrue()
        ->and($whColumns->has('assets_sum_cost'))->toBeTrue();

    $whRow = AssetWarehouseExporter::modifyQuery(AssetWarehouse::query()->whereKey($warehouse->id))->first();
    expect($whRow->assets_count)->toBe(1)
        ->and((float) $whRow->assets_sum_cost)->toBe(500.00);

    // Site Exporter
    $siteColumns = collect(AssetSiteExporter::getColumns())->keyBy(fn ($c) => $c->getName());
    expect($siteColumns->has('parent.full_name'))->toBeTrue()
        ->and($siteColumns->has('assets_count'))->toBeTrue()
        ->and($siteColumns->has('assets_sum_cost'))->toBeTrue();

    $siteRow = AssetSiteExporter::modifyQuery(AssetSite::query()->whereKey($site->id))->first();
    expect($siteRow->assets_count)->toBe(1)
        ->and((float) $siteRow->assets_sum_cost)->toBe(500.00);

    // Category Exporter
    $catColumns = collect(AssetCategoryExporter::getColumns())->keyBy(fn ($c) => $c->getName());
    expect($catColumns->has('products_count'))->toBeTrue()
        ->and($catColumns->has('assets_count'))->toBeTrue()
        ->and($catColumns->has('assets_sum_cost'))->toBeTrue();

    $catRow = AssetCategoryExporter::modifyQuery(AssetCategory::query()->whereKey($cat->id))->first();
    expect($catRow->products_count)->toBe(1)
        ->and($catRow->assets_count)->toBe(2)
        ->and((float) $catRow->assets_sum_cost)->toBe(1000.00);
});

it('generates descriptive, sanitized filenames for exports', function () {
    $export = new Export;
    $export->options([
        'context'      => 'Warehouse / HQ',
        'statuses'     => [AssetStatus::Available->value],
        'product_ids'  => [1, 2],
    ]);

    $exporter = new AssetExporter($export, [], $export->getOptions());
    $fileName = $exporter->getFileName($export);

    expect($fileName)->toContain('assets')
        ->and($fileName)->toContain('Warehouse-HQ')
        ->and($fileName)->toContain(now()->format('Y-m-d'))
        ->and($fileName)->not->toContain('/')
        ->and($fileName)->not->toContain('\\');
});

it('registers a single floating horizontal scrollbar without injecting nodes into the table DOM', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $viewManager = app(ViewManager::class);

    $styles = $viewManager->renderHook(PanelsRenderHook::STYLES_AFTER)->toHtml();

    expect($styles)
        ->toContain('.fi-ta-floating-hscroll')
        ->toContain('.fi-ta-sticky-scroll .fi-ta-content-ctn')
        ->toContain('.fi-ta-hscroll-track')
        ->not->toContain('.fi-ta-top-hscroll')
        ->not->toContain('.fi-ta-bottom-hscroll')
        ->not->toContain('max-height: calc(100vh - 15rem)')
        ->not->toContain('thead');

    $scripts = $viewManager->renderHook(PanelsRenderHook::SCRIPTS_AFTER)->toHtml();

    expect($scripts)
        ->toContain('fi-ta-floating-hscroll')
        ->toContain('document.body.append')
        ->toContain('fi-ta-sticky-scroll .fi-ta-content-ctn')
        ->toContain('ResizeObserver')
        ->toContain('MutationObserver')
        ->toContain('livewire:navigated')
        ->not->toContain('fi-ta-top-hscroll')
        ->not->toContain('fi-ta-bottom-hscroll')
        ->not->toContain('contentCtn.before')
        ->not->toContain('contentCtn.after');

    $html = Livewire::test(ListAssets::class)->assertOk()->html();

    expect($html)
        ->toContain('fi-ta-sticky-scroll')
        ->not->toContain('fi-ta-top-hscroll')
        ->not->toContain('fi-ta-bottom-hscroll');

    $table = Livewire::test(ListAssets::class)->assertOk()->instance()->getTable();

    expect($table->getExtraAttributes()['class'])->toContain('fi-ta-sticky-scroll');
});

it('keeps the assets table rendering across page size changes up to 100 records', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->company($company)->create();
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();

    Asset::factory()
        ->count(105)
        ->company($company)
        ->forProduct($product)
        ->available($warehouse)
        ->sequence(fn ($sequence): array => [
            'asset_tag' => 'AST-'.str_pad((string) ($sequence->index + 1), 5, '0', STR_PAD_LEFT),
        ])
        ->create();

    $livewire = Livewire::test(ListAssets::class)->assertOk();

    $renderedRecords = function () use ($livewire): array {
        $records = $livewire->instance()->getTable()->getRecords();

        return collect($records instanceof Paginator ? $records->items() : $records)->all();
    };

    foreach ([25, 50, 100, 50] as $perPage) {
        $livewire->set('tableRecordsPerPage', $perPage)->assertOk();

        expect($renderedRecords())->toHaveCount($perPage);

        $livewire->assertCanSeeTableRecords($renderedRecords());
    }
});

it('pre-fills asset export modal options from active table filters', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();
    $product = AssetProduct::factory()->company($company)->create();
    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);

    $livewire = Livewire::test(ListAssets::class)
        ->filterTable('warehouse_id', [$warehouse->id])
        ->filterTable('product_id', [$product->id])
        ->filterTable('status', AssetStatus::Held->value)
        ->filterTable('current_employee_id', [$employee->id])
        ->instance();

    $selects = collect(AssetExporter::getOptionsFormComponents()[0]->getDefaultChildComponents())
        ->keyBy(fn ($component): string => $component->getName());

    foreach ($selects as $select) {
        expect($select->isDehydrated())->toBeTrue();
    }

    $resolveDefault = function (string $componentName) use ($selects, $livewire): array {
        $select = $selects[$componentName];

        $default = (new ReflectionProperty($select, 'defaultState'))->getValue($select);

        expect($default)->toBeInstanceOf(Closure::class);

        return $default($livewire);
    };

    expect($resolveDefault('warehouse_ids'))->toBe([$warehouse->id])
        ->and($resolveDefault('product_ids'))->toBe([$product->id])
        ->and($resolveDefault('statuses'))->toBe([AssetStatus::Held->value])
        ->and($resolveDefault('employee_ids'))->toBe([$employee->id])
        ->and($resolveDefault('site_ids'))->toBe([]);

    $movementLivewire = Livewire::test(ListAssetMovements::class)
        ->filterTable('occurred_at', ['from' => '2026-01-01', 'until' => '2026-01-31'])
        ->filterTable('type', [MovementType::Issued->value])
        ->filterTable('employee_id', [$employee->id])
        ->instance();

    $movementFields = collect(AssetMovementExporter::getOptionsFormComponents()[0]->getDefaultChildComponents())
        ->keyBy(fn ($component): string => $component->getName());

    $resolveMovementDefault = function (string $componentName) use ($movementFields, $movementLivewire): mixed {
        $field = $movementFields[$componentName];

        $default = (new ReflectionProperty($field, 'defaultState'))->getValue($field);

        return $default($movementLivewire);
    };

    expect($resolveMovementDefault('date_from'))->toBe('2026-01-01')
        ->and($resolveMovementDefault('date_until'))->toBe('2026-01-31')
        ->and($resolveMovementDefault('types'))->toBe([MovementType::Issued->value])
        ->and($resolveMovementDefault('employee_ids'))->toBe([$employee->id])
        ->and($resolveMovementDefault('site_ids'))->toBe([]);
});

it('sums the cost of all product assets in the product exporter', function () {
    FilamentHelper::actingAs(['view_any_asset_product']);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->active()->create();
    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
    ]);

    $product = AssetProduct::factory()->company($company)->create(['cost' => 100.00]);

    Asset::factory()->company($company)->forProduct($product)->available($warehouse)->create(['cost' => 300.00]);
    Asset::factory()->company($company)->forProduct($product)->heldByEmployee($employee)->create(['cost' => 200.00]);
    Asset::factory()->company($company)->forProduct($product)->create([
        'status'              => AssetStatus::Scrapped,
        'warehouse_id'        => null,
        'current_employee_id' => null,
        'current_site_id'     => null,
        'cost'                => 100.00,
    ]);

    $row = AssetProductExporter::modifyQuery(AssetProduct::query()->whereKey($product->id))->first();

    expect((float) $row->assets_sum_cost)->toBe(600.00);

    Livewire::test(ListProducts::class)
        ->assertOk()
        ->assertTableColumnStateSet('assets_sum_cost', 600.00, record: $product);
});

it('renders export filter options above the column selection fieldset in export modals', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $componentClasses = function (ExportAction $action, HasSchemas $livewire): array {
        return collect($action->getSchema(Schema::make($livewire))->getComponents())
            ->map(fn ($component): string => $component::class)
            ->all();
    };

    $assetsLivewire = Livewire::test(ListAssets::class)->assertOk()->instance();

    $assetsAction = AssetExporter::configureExportAction(
        ExportAction::make('export')->exporter(AssetExporter::class)
    );

    $assetsComponents = $componentClasses($assetsAction, $assetsLivewire);

    expect($assetsComponents[0])
        ->toBe(Section::class)
        ->and(end($assetsComponents))->toBe(Fieldset::class);

    $movementsLivewire = Livewire::test(ListAssetMovements::class)->assertOk()->instance();

    $movementsAction = AssetMovementExporter::configureExportAction(
        ExportAction::make('export')->exporter(AssetMovementExporter::class)
    );

    $movementsComponents = $componentClasses($movementsAction, $movementsLivewire);

    expect($movementsComponents[0])
        ->toBe(Section::class)
        ->and(end($movementsComponents))->toBe(Fieldset::class);
});
