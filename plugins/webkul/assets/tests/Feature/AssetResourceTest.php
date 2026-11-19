<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

use Livewire\Livewire;
use Webkul\Asset\AssetServiceProvider;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\IssueAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\CreateAsset;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\EditAsset;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\ListAssets;
use Webkul\Asset\Filament\Resources\AssetResource\Pages\ViewAsset;
use Webkul\Asset\Filament\Resources\AssetResource\RelationManagers\AssignmentsRelationManager;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

function createAssetEmployee(Company $company, array $attributes = []): Employee
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

    $settings = app(AssetSettings::class);
    $settings->tag_prefix = 'AST-';
    $settings->auto_generate_tag = true;
    $settings->default_condition = 'new';
    $settings->require_serial_by_default = false;
    $settings->default_warehouse_id = null;
    $settings->require_site_on_custody = false;
    $settings->require_attachments_on_custody = false;
    $settings->require_attachments_on_damage = false;
    $settings->enable_consumables_batch_issue = true;
    $settings->auto_fill_consumable_employees_by_site = true;
    $settings->default_consumable_issue_quantity = 1;
    $settings->require_site_on_consumable_issue = false;
    $settings->require_attachments_on_consumable_issue = false;
    $settings->save();
});

it('forbids listing company assets without permission (Test 18)', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListAssets::class)->assertForbidden();
});

it('allows listing company assets with permission and renders table (Test 18)', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();

    $productA = AssetProduct::factory()->create([
        'name'       => 'MacBook Pro M3',
        'company_id' => $company->id,
    ]);
    $assetA = Asset::factory()->available()->forProduct($productA)->create([
        'company_id' => $company->id,
    ]);

    $productB = AssetProduct::factory()->create([
        'name'       => 'Dell UltraSharp Monitor',
        'company_id' => $company->id,
    ]);
    $assetB = Asset::factory()->available()->forProduct($productB)->create([
        'company_id' => $company->id,
    ]);

    Livewire::test(ListAssets::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$assetA, $assetB])
        ->searchTable('MacBook Pro')
        ->assertCanSeeTableRecords([$assetA])
        ->assertCanNotSeeTableRecords([$assetB]);
});

it('creates an asset and auto-fills attributes and tag according to settings (Test 11)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $settings = app(AssetSettings::class);
    $settings->auto_generate_tag = true;
    $settings->require_serial_by_default = false;
    $settings->default_warehouse_id = $warehouse->id;
    $settings->save();

    $category = AssetCategory::factory()->create(['name' => 'Laptops']);
    $product = AssetProduct::factory()->create([
        'name'        => 'ThinkPad X1 Carbon',
        'model'       => 'X1C-GEN11',
        'cost'        => 5500.00,
        'category_id' => $category->id,
        'company_id'  => $company->id,
    ]);

    Livewire::test(CreateAsset::class)
        ->fillForm([
            'company_id'   => $company->id,
            'product_id'   => $product->id,
            'warehouse_id' => $warehouse->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $created = Asset::query()->where('product_id', $product->id)->firstOrFail();
    expect($created->name)->toBe('ThinkPad X1 Carbon')
        ->and($created->asset_tag)->toBe('AST-00001')
        ->and($created->status)->toBe(AssetStatus::Available)
        ->and($created->warehouse_id)->toBe($warehouse->id);
});

it('ensures ledger rows expose no edit or delete actions on relation manager (Test 20)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $employee = createAssetEmployee($company);
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $asset = Asset::factory()->available()->create([
        'company_id'   => $company->id,
        'warehouse_id' => $warehouse->id,
    ]);

    $movement = AssetMovement::factory()->issue()->create([
        'asset_id'       => $asset->id,
        'company_id'     => $company->id,
        'to_employee_id' => $employee->id,
        'occurred_at'    => now(),
    ]);

    $manager = Livewire::test(AssignmentsRelationManager::class, [
        'ownerRecord' => $asset,
        'pageClass'   => ViewAsset::class,
    ]);

    $manager
        ->assertOk()
        ->assertCanSeeTableRecords([$movement])
        ->assertActionDoesNotExist('edit')
        ->assertActionDoesNotExist('delete')
        ->assertTableActionDoesNotExist('edit')
        ->assertTableActionDoesNotExist('delete');
});

it('filters assets by preset table views', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();
    $employee = createAssetEmployee($company);
    $site = AssetSite::factory()->active()->create(['company_id' => $company->id]);

    $availableAsset = Asset::factory()->available()->create([
        'company_id' => $company->id,
    ]);

    $heldEmployeeAsset = Asset::factory()->heldByEmployee($employee)->create([
        'company_id' => $company->id,
    ]);

    $heldSiteAsset = Asset::factory()->heldBySite($site)->create([
        'company_id' => $company->id,
    ]);

    $test = Livewire::test(ListAssets::class);

    $test
        ->assertOk()
        ->assertCanSeeTableRecords([$availableAsset, $heldEmployeeAsset, $heldSiteAsset]);
});

it('edits an asset record', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $productA = AssetProduct::factory()->create(['name' => 'Original Product', 'company_id' => $company->id]);
    $productB = AssetProduct::factory()->create(['name' => 'Updated Product', 'company_id' => $company->id]);
    $asset = Asset::factory()->available()->forProduct($productA)->create([
        'company_id' => $company->id,
        'note'       => 'Original note',
    ]);

    Livewire::test(EditAsset::class, ['record' => $asset->id])
        ->fillForm([
            'product_id' => $productB->id,
            'note'       => 'Updated note',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($asset->fresh()->product_id)->toBe($productB->id)
        ->and($asset->fresh()->name)->toBe('Updated Product')
        ->and($asset->fresh()->note)->toBe('Updated note');
});

it('issues custody to employee and to site (Tests 1, 2, 3)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $employee = createAssetEmployee($company);
    $receiver = createAssetEmployee($company);
    $site = AssetSite::factory()->active()->custodyZone()->create([
        'company_id' => $company->id,
    ]);
    $warehouse = AssetWarehouse::factory()->company($company)->create();

    $asset = Asset::factory()->available()->create([
        'company_id'   => $company->id,
        'warehouse_id' => $warehouse->id,
    ]);

    Livewire::test(ListAssets::class)
        ->callTableAction('issue', $asset, data: [
            'employee_id'   => $employee->id,
            'condition_out' => AssetCondition::Good->value,
            'issued_at'     => now(),
        ])
        ->assertHasNoTableActionErrors();

    $asset->refresh();
    expect($asset->status)->toBe(AssetStatus::Held)
        ->and($asset->current_employee_id)->toBe($employee->id)
        ->and($asset->warehouse_id)->toBeNull();

    Livewire::test(ListAssets::class)
        ->callTableAction('return', $asset, data: [
            'condition_in' => AssetCondition::Good->value,
            'ended_at'     => now(),
            'warehouse_id' => $warehouse->id,
        ])
        ->assertHasNoTableActionErrors();

    $asset->refresh();
    expect($asset->status)->toBe(AssetStatus::Available)
        ->and($asset->warehouse_id)->toBe($warehouse->id)
        ->and($asset->current_employee_id)->toBeNull();

    Livewire::test(ListAssets::class)
        ->callTableAction('issue', $asset, data: [
            'employee_id'   => $receiver->id,
            'site_id'       => $site->id,
            'condition_out' => AssetCondition::Good->value,
            'issued_at'     => now(),
        ])
        ->assertHasNoTableActionErrors();

    $asset->refresh();
    expect($asset->status)->toBe(AssetStatus::Held)
        ->and($asset->current_site_id)->toBe($site->id)
        ->and($asset->current_employee_id)->toBe($receiver->id)
        ->and($asset->warehouse_id)->toBeNull();
});

it('transfers custody between employees (Test 4)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $employeeA = createAssetEmployee($company);
    $employeeB = createAssetEmployee($company);

    $asset = Asset::factory()->heldByEmployee($employeeA)->create([
        'company_id' => $company->id,
        'issued_at'  => now()->subDay(),
    ]);

    Livewire::test(ListAssets::class)
        ->callTableAction('transfer', $asset, data: [
            'employee_id'  => $employeeB->id,
            'condition_in' => AssetCondition::Good->value,
            'issued_at'    => now(),
        ])
        ->assertHasNoTableActionErrors();

    $asset->refresh();
    expect($asset->current_employee_id)->toBe($employeeB->id)
        ->and($asset->movements()->count())->toBe(3);
});

it('returns asset and enforces return note on damaged condition (Test 5)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $employee = createAssetEmployee($company);
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $asset = Asset::factory()->heldByEmployee($employee)->create(['company_id' => $company->id]);

    Livewire::test(ListAssets::class)
        ->callTableAction('return', $asset, data: [
            'condition_in' => AssetCondition::Damaged->value,
            'ended_at'     => now(),
            'warehouse_id' => $warehouse->id,
            'return_note'  => '',
        ])
        ->assertHasTableActionErrors(['return_note']);

    Livewire::test(ListAssets::class)
        ->callTableAction('return', $asset, data: [
            'condition_in' => AssetCondition::Damaged->value,
            'ended_at'     => now(),
            'warehouse_id' => $warehouse->id,
            'return_note'  => 'Broken screen during transit',
        ])
        ->assertHasNoTableActionErrors();

    $asset->refresh();
    expect($asset->status)->toBe(AssetStatus::Available)
        ->and($asset->condition)->toBe(AssetCondition::Damaged)
        ->and($asset->current_employee_id)->toBeNull()
        ->and($asset->warehouse_id)->toBe($warehouse->id);
});

it('scraps and marks assets as lost (Test 6)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
        'delete_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $employee = createAssetEmployee($company);

    $availableAsset = Asset::factory()->available()->create(['company_id' => $company->id]);
    $heldAsset = Asset::factory()->heldByEmployee($employee)->create(['company_id' => $company->id]);

    Livewire::test(ListAssets::class)
        ->callTableAction('scrap', $availableAsset, data: [
            'ended_at' => now(),
            'note'     => 'Beyond repair',
        ])
        ->assertHasNoTableActionErrors();

    expect($availableAsset->fresh()->status)->toBe(AssetStatus::Scrapped);

    Livewire::test(ListAssets::class)
        ->callTableAction('markLost', $heldAsset, data: [
            'ended_at' => now(),
            'note'     => 'Lost in field site',
        ])
        ->assertHasNoTableActionErrors();

    $heldAsset->refresh();
    expect($heldAsset->status)->toBe(AssetStatus::Lost)
        ->and($heldAsset->current_employee_id)->toBeNull()
        ->and($heldAsset->current_site_id)->toBeNull()
        ->and($heldAsset->warehouse_id)->toBeNull();
});

it('bulk registers serialized units via header action (Test 13)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->create([
        'name'       => 'Dell Latitude 5540',
        'company_id' => $company->id,
    ]);

    Livewire::test(ListAssets::class)
        ->callAction('bulkRegister', data: [
            'company_id'    => $company->id,
            'product_id'    => $product->id,
            'warehouse_id'  => $warehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                ['serial_no' => 'SN-DELL-001'],
                ['serial_no' => 'SN-DELL-002'],
                ['serial_no' => 'SN-DELL-003'],
            ],
        ])
        ->assertHasNoActionErrors();

    expect(Asset::query()->where('product_id', $product->id)->count())->toBe(3)
        ->and(Asset::query()->where('serial_no', 'SN-DELL-001')->exists())->toBeTrue()
        ->and(Asset::query()->where('warehouse_id', $warehouse->id)->count())->toBe(3);
});

it('rejects bulk registration on duplicate serials or DB collisions (Test 13)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->create(['company_id' => $company->id]);

    Asset::factory()->available()->create([
        'company_id'   => $company->id,
        'warehouse_id' => $warehouse->id,
        'serial_no'    => 'EXISTING-SERIAL',
    ]);

    Livewire::test(ListAssets::class)
        ->callAction('bulkRegister', data: [
            'company_id'    => $company->id,
            'product_id'    => $product->id,
            'warehouse_id'  => $warehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                ['serial_no' => 'SN-BATCH-001'],
                ['serial_no' => 'SN-BATCH-001'],
            ],
        ])
        ->assertHasActionErrors();

    Livewire::test(ListAssets::class)
        ->callAction('bulkRegister', data: [
            'company_id'    => $company->id,
            'product_id'    => $product->id,
            'warehouse_id'  => $warehouse->id,
            'tracking_mode' => 'with_serials',
            'items'         => [
                ['serial_no' => 'SN-BATCH-NEW'],
                ['serial_no' => 'EXISTING-SERIAL'],
            ],
        ])
        ->assertHasActionErrors();

    expect(Asset::query()->where('serial_no', 'SN-BATCH-NEW')->exists())->toBeFalse();
});

it('validates duplicate asset tag in form and prevents 500 error', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->create(['company_id' => $company->id]);

    Asset::factory()->available()->create([
        'company_id'   => $company->id,
        'warehouse_id' => $warehouse->id,
        'asset_tag'    => 'AST-DUP-TEST',
    ]);

    Livewire::test(CreateAsset::class)
        ->fillForm([
            'product_id'   => $product->id,
            'company_id'   => $company->id,
            'warehouse_id' => $warehouse->id,
            'asset_tag'    => 'AST-DUP-TEST',
        ])
        ->call('create')
        ->assertHasFormErrors(['asset_tag']);
});

it('validates duplicate serial number in form and prevents 500 error', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->create(['company_id' => $company->id]);

    Asset::factory()->available()->create([
        'company_id'   => $company->id,
        'warehouse_id' => $warehouse->id,
        'serial_no'    => 'SN-DUP-TEST',
    ]);

    Livewire::test(CreateAsset::class)
        ->fillForm([
            'product_id'   => $product->id,
            'company_id'   => $company->id,
            'warehouse_id' => $warehouse->id,
            'serial_no'    => 'SN-DUP-TEST',
        ])
        ->call('create')
        ->assertHasFormErrors(['serial_no']);
});

it('halts cleanly with a notification on bulk delete when a held asset is selected (Issue 11)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'delete_asset_asset',
        'delete_any_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $availableAsset = Asset::factory()->company($company)->available()->create();
    $heldAsset = Asset::factory()->company($company)->heldByEmployee()->create();

    Livewire::test(ListAssets::class)
        ->callTableBulkAction('delete', [$availableAsset, $heldAsset])
        ->assertNotified();

    expect($availableAsset->fresh()->trashed())->toBeFalse()
        ->and($heldAsset->fresh()->trashed())->toBeFalse();
});

it('returns an asset into a specified warehouse and clears holder (Issue 01)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $employee = createAssetEmployee($company);
    $warehouse = AssetWarehouse::factory()->company($company)->create();

    $asset = Asset::factory()->company($company)->heldByEmployee($employee)->create();

    Livewire::test(ListAssets::class)
        ->callTableAction('return', $asset, data: [
            'condition_in' => AssetCondition::Good->value,
            'ended_at'     => now(),
            'warehouse_id' => $warehouse->id,
        ])
        ->assertHasNoTableActionErrors();

    $asset->refresh();
    expect($asset->status)->toBe(AssetStatus::Available)
        ->and($asset->current_employee_id)->toBeNull()
        ->and($asset->current_site_id)->toBeNull()
        ->and($asset->warehouse_id)->toBe($warehouse->id);
});

it('filters assets by current employee', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();
    $employee = createAssetEmployee($company);
    $otherEmployee = createAssetEmployee($company);

    $directAsset = Asset::factory()->company($company)->heldByEmployee($employee)->create();
    $otherAsset = Asset::factory()->company($company)->heldByEmployee($otherEmployee)->create();

    Livewire::test(ListAssets::class)
        ->assertOk()
        ->filterTable('current_employee_id', $employee->id)
        ->assertCanSeeTableRecords([$directAsset])
        ->assertCanNotSeeTableRecords([$otherAsset]);
});

it('filters assets by department, product, and site type', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();
    $department = Department::factory()->create(['company_id' => $company->id]);
    $deptEmployee = createAssetEmployee($company, ['department_id' => $department->id]);
    $otherEmployee = createAssetEmployee($company);

    $deptAsset = Asset::factory()->company($company)->heldByEmployee($deptEmployee)->create();
    $otherDeptAsset = Asset::factory()->company($company)->heldByEmployee($otherEmployee)->create();

    $productA = AssetProduct::factory()->create(['company_id' => $company->id]);
    $productB = AssetProduct::factory()->create(['company_id' => $company->id]);
    $prodAssetA = Asset::factory()->company($company)->available()->forProduct($productA)->create();
    $prodAssetB = Asset::factory()->company($company)->available()->forProduct($productB)->create();

    $siteType = AssetSiteType::factory()->create();
    $site = AssetSite::factory()->company($company)->custodyZone()->create(['site_type_id' => $siteType->id]);
    $siteAsset = Asset::factory()->company($company)->heldByEmployee($deptEmployee, $site)->create();
    $otherSiteAsset = Asset::factory()->company($company)->heldByEmployee($otherEmployee)->create();

    Livewire::test(ListAssets::class)
        ->filterTable('department_id', $department->id)
        ->assertCanSeeTableRecords([$deptAsset, $siteAsset])
        ->assertCanNotSeeTableRecords([$otherDeptAsset])
        ->filterTable('department_id', null)
        ->filterTable('product_id', $productA->id)
        ->assertCanSeeTableRecords([$prodAssetA])
        ->assertCanNotSeeTableRecords([$prodAssetB])
        ->filterTable('product_id', null)
        ->filterTable('site_type_id', $siteType->id)
        ->assertCanSeeTableRecords([$siteAsset])
        ->assertCanNotSeeTableRecords([$otherSiteAsset]);
});

it('bulk registers asset units using repeater items with custom condition, note, and warehouse (Issues 01, 05, 10)', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->create(['company_id' => $company->id]);
    $warehouse = AssetWarehouse::factory()->company($company)->create();

    Livewire::test(ListAssets::class)
        ->callAction('bulkRegister', data: [
            'company_id'    => $company->id,
            'product_id'    => $product->id,
            'tracking_mode' => 'with_serials',
            'warehouse_id'  => $warehouse->id,
            'items'         => [
                [
                    'serial_no' => 'SN-CUST-001',
                    'asset_tag' => 'AST-CUST-001',
                    'condition' => AssetCondition::New->value,
                    'note'      => 'Factory sealed',
                ],
                [
                    'serial_no' => 'SN-CUST-002',
                    'asset_tag' => 'AST-CUST-002',
                    'condition' => AssetCondition::Good->value,
                    'note'      => 'Opened box demo',
                ],
            ],
        ])
        ->assertHasNoActionErrors();

    $unit1 = Asset::query()->where('serial_no', 'SN-CUST-001')->firstOrFail();
    expect($unit1->condition)->toBe(AssetCondition::New)
        ->and($unit1->note)->toBe('Factory sealed')
        ->and($unit1->warehouse_id)->toBe($warehouse->id);

    $unit2 = Asset::query()->where('serial_no', 'SN-CUST-002')->firstOrFail();
    expect($unit2->condition)->toBe(AssetCondition::Good)
        ->and($unit2->note)->toBe('Opened box demo')
        ->and($unit2->warehouse_id)->toBe($warehouse->id);
});

it('creates an asset directly as a fixed site asset or employee custody via initial placement', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->create(['company_id' => $company->id]);
    $siteLocation = AssetSite::factory()->company($company)->assetLocation()->create();
    $employee = createAssetEmployee($company);
    $custodyZone = AssetSite::factory()->company($company)->custodyZone()->create([
        'default_custodian_employee_id' => $employee->id,
    ]);

    Livewire::test(CreateAsset::class)
        ->fillForm([
            'product_id'        => $product->id,
            'company_id'        => $company->id,
            'initial_placement' => 'employee_custody',
            'current_site_id'   => $custodyZone->id,
        ])
        ->assertFormSet([
            'current_employee_id' => $employee->id,
        ]);

    Livewire::test(CreateAsset::class)
        ->fillForm([
            'product_id'        => $product->id,
            'company_id'        => $company->id,
            'initial_placement' => 'site_asset',
            'current_site_id'   => $siteLocation->id,
            'serial_no'         => 'SN-DIRECT-SITE-01',
            'issued_at'         => '2026-03-01 10:00:00',
            'custody_note'      => 'Installed in server rack A',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $siteAsset = Asset::query()->where('serial_no', 'SN-DIRECT-SITE-01')->firstOrFail();
    expect($siteAsset->status)->toBe(AssetStatus::Held)
        ->and($siteAsset->current_site_id)->toBe($siteLocation->id)
        ->and($siteAsset->current_employee_id)->toBeNull()
        ->and($siteAsset->warehouse_id)->toBeNull()
        ->and($siteAsset->custody_note)->toBe('Installed in server rack A');

    Livewire::test(CreateAsset::class)
        ->fillForm([
            'product_id'          => $product->id,
            'company_id'          => $company->id,
            'initial_placement'   => 'employee_custody',
            'current_employee_id' => $employee->id,
            'current_site_id'     => $custodyZone->id,
            'serial_no'           => 'SN-DIRECT-EMP-01',
            'issued_at'           => '2026-03-02 11:00:00',
            'expected_return_at'  => '2026-06-01',
            'custody_note'        => 'Assigned laptop for remote work',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $empAsset = Asset::query()->where('serial_no', 'SN-DIRECT-EMP-01')->firstOrFail();
    expect($empAsset->status)->toBe(AssetStatus::Held)
        ->and($empAsset->current_employee_id)->toBe($employee->id)
        ->and($empAsset->current_site_id)->toBe($custodyZone->id)
        ->and($empAsset->warehouse_id)->toBeNull()
        ->and($empAsset->expected_return_at->toDateString())->toBe('2026-06-01')
        ->and($empAsset->custody_note)->toBe('Assigned laptop for remote work');
});

it('issues and transfers custody using allocation_mode in table actions and filters by site subtree', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $parentSite = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'HQ Building']);
    $childSite = AssetSite::factory()->company($company)->withParent($parentSite)->create(['name' => 'HQ Floor 2']);
    $otherSite = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Remote Site']);

    $availableAsset = Asset::factory()->company($company)->available($warehouse)->create();

    Livewire::test(ListAssets::class)
        ->callTableAction('issue', $availableAsset, data: [
            'allocation_mode' => 'site_asset',
            'site_id'         => $childSite->id,
            'condition_out'   => AssetCondition::New->value,
            'issued_at'       => now(),
        ])
        ->assertHasNoTableActionErrors();

    $availableAsset->refresh();
    expect($availableAsset->status)->toBe(AssetStatus::Held)
        ->and($availableAsset->current_site_id)->toBe($childSite->id)
        ->and($availableAsset->current_employee_id)->toBeNull();

    Livewire::test(ListAssets::class)
        ->callTableAction('transfer', $availableAsset, data: [
            'allocation_mode' => 'site_asset',
            'site_id'         => $childSite->id,
            'condition_in'    => AssetCondition::New->value,
            'issued_at'       => now(),
        ])
        ->assertHasTableActionErrors();

    $otherAsset = Asset::factory()->company($company)->create([
        'status'              => AssetStatus::Held,
        'current_site_id'     => $otherSite->id,
        'current_employee_id' => null,
        'warehouse_id'        => null,
    ]);

    Livewire::test(ListAssets::class)
        ->filterTable('current_site_id', $parentSite->id)
        ->assertCanSeeTableRecords([$availableAsset])
        ->assertCanNotSeeTableRecords([$otherAsset]);

    $emp = createAssetEmployee($company);
    $empAsset = Asset::factory()->company($company)->heldByEmployee($emp)->create();

    Livewire::test(ListAssets::class)
        ->call('loadView', 'site_assets')
        ->assertCanSeeTableRecords([$availableAsset, $otherAsset])
        ->assertCanNotSeeTableRecords([$empAsset]);

    Livewire::test(ListAssets::class)
        ->call('loadView', 'with_employees')
        ->assertCanSeeTableRecords([$empAsset])
        ->assertCanNotSeeTableRecords([$availableAsset, $otherAsset]);
});

it('exposes direct custody header actions on view and edit asset pages and filters site options by usage type', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $locationSite = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Data Center Site']);
    $custodySite = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'Branch Office Zone']);
    $employee = createAssetEmployee($company);

    $availableAsset = Asset::factory()->available()->company($company)->create([
        'warehouse_id' => $warehouse->id,
    ]);

    Livewire::test(ViewAsset::class, ['record' => $availableAsset->getRouteKey()])
        ->assertActionVisible('issue')
        ->assertActionHidden('transfer')
        ->assertActionHidden('return');

    Livewire::test(EditAsset::class, ['record' => $availableAsset->getRouteKey()])
        ->assertActionVisible('issue')
        ->assertActionHidden('transfer')
        ->assertActionHidden('return');

    $heldAsset = Asset::factory()->heldByEmployee($employee)->company($company)->create();

    Livewire::test(ViewAsset::class, ['record' => $heldAsset->getRouteKey()])
        ->assertActionHidden('issue')
        ->assertActionVisible('transfer')
        ->assertActionVisible('return');

    Livewire::test(EditAsset::class, ['record' => $heldAsset->getRouteKey()])
        ->assertActionHidden('issue')
        ->assertActionVisible('transfer')
        ->assertActionVisible('return');
});

it('shows archived assets under archived preset view and allows restore or permanent delete', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'restore_asset_asset',
        'force_delete_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $activeAsset = Asset::factory()->available()->company($company)->create();
    $archivedAsset = Asset::factory()->available()->company($company)->create();
    $archivedAsset->delete();

    expect($archivedAsset->fresh()->trashed())->toBeTrue();

    Livewire::test(ListAssets::class)
        ->assertCanSeeTableRecords([$activeAsset])
        ->assertCanNotSeeTableRecords([$archivedAsset]);

    Livewire::test(ListAssets::class)
        ->call('loadView', 'archived')
        ->assertCanSeeTableRecords([$archivedAsset])
        ->assertCanNotSeeTableRecords([$activeAsset])
        ->callTableAction('restore', $archivedAsset);

    expect($archivedAsset->fresh()->trashed())->toBeFalse();

    $archivedAsset->delete();
    expect($archivedAsset->fresh()->trashed())->toBeTrue();

    Livewire::test(ListAssets::class)
        ->call('loadView', 'archived')
        ->assertCanSeeTableRecords([$archivedAsset])
        ->callTableAction('forceDelete', $archivedAsset);

    expect(Asset::withTrashed()->find($archivedAsset->id))->toBeNull();
});

it('hides editOptionAction on product_id when empty and shows it when selected', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
        'update_asset_product',
    ]);

    $company = Company::query()->firstOrFail();
    $product = AssetProduct::factory()->company($company)->create();

    $livewire = Livewire::test(CreateAsset::class);
    $component = $livewire->instance()->getSchema('form')->getFlatComponents()['product_id'];
    $action = $component->getAction('editOption');

    expect($action->isVisible())->toBeFalse();

    $livewire->fillForm(['product_id' => $product->id]);
    $component = $livewire->instance()->getSchema('form')->getFlatComponents()['product_id'];
    $action = $component->getAction('editOption');

    expect($action->isVisible())->toBeTrue();
});

it('supports multi-select filtering on products, categories, and sites', function () {
    FilamentHelper::actingAs(['view_any_asset_asset']);

    $company = Company::query()->firstOrFail();
    $cat1 = AssetCategory::factory()->company($company)->create(['name' => 'Computers Cat']);
    $cat2 = AssetCategory::factory()->company($company)->create(['name' => 'Peripherals Cat']);
    $cat3 = AssetCategory::factory()->company($company)->create(['name' => 'Furniture Cat']);

    $prod1 = AssetProduct::factory()->company($company)->create(['category_id' => $cat1->id]);
    $prod2 = AssetProduct::factory()->company($company)->create(['category_id' => $cat2->id]);
    $prod3 = AssetProduct::factory()->company($company)->create(['category_id' => $cat3->id]);

    $site1 = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Multi Site A']);
    $site2 = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Multi Site B']);
    $site3 = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Multi Site C']);

    $asset1 = Asset::factory()->company($company)->forProduct($prod1)->heldBySite($site1)->create();
    $asset2 = Asset::factory()->company($company)->forProduct($prod2)->heldBySite($site2)->create();
    $asset3 = Asset::factory()->company($company)->forProduct($prod3)->heldBySite($site3)->create();

    Livewire::test(ListAssets::class)
        ->filterTable('product_id', [$prod1->id, $prod2->id])
        ->assertCanSeeTableRecords([$asset1, $asset2])
        ->assertCanNotSeeTableRecords([$asset3]);

    Livewire::test(ListAssets::class)
        ->filterTable('category_id', [$cat1->id, $cat2->id])
        ->assertCanSeeTableRecords([$asset1, $asset2])
        ->assertCanNotSeeTableRecords([$asset3]);

    Livewire::test(ListAssets::class)
        ->filterTable('current_site_id', [$site1->id, $site2->id])
        ->assertCanSeeTableRecords([$asset1, $asset2])
        ->assertCanNotSeeTableRecords([$asset3]);
});

it('creates a consumable batch with quantity from the asset form without a serial number', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
        'view_any_asset_product',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);

    Livewire::test(CreateAsset::class)
        ->fillForm([
            'company_id'            => $company->id,
            'product_id'            => $product->id,
            'selected_product_type' => AssetProductType::Consumable->value,
            'warehouse_id'          => $warehouse->id,
            'initial_quantity'      => 50,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $batch = Asset::query()->where('product_id', $product->id)->firstOrFail();

    expect($batch->isConsumable())->toBeTrue()
        ->and((float) $batch->initial_quantity)->toBe(50.0)
        ->and((float) $batch->quantity)->toBe(50.0)
        ->and($batch->serial_no)->toBeNull()
        ->and($batch->status)->toBe(AssetStatus::Available);
});

it('issues consumable quantities to multiple employees from the asset table', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);

    $emp1 = createAssetEmployee($company);
    $emp2 = createAssetEmployee($company);
    $emp3 = createAssetEmployee($company);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 50,
    ]);

    Livewire::test(ListAssets::class)
        ->callTableAction('issue', $batch, data: [
            'employee_ids' => [$emp1->id, $emp2->id, $emp3->id],
            'quantity'     => 5,
            'issued_at'    => now(),
        ])
        ->assertHasNoTableActionErrors();

    $batch->refresh();

    expect((float) $batch->quantity)->toBe(35.0)
        ->and($batch->status)->toBe(AssetStatus::Available)
        ->and($batch->movements()->where('type', MovementType::Issued)->count())->toBe(3)
        ->and((float) $batch->movements()->where('type', MovementType::Issued)->sum('quantity'))->toBe(15.0);
});

it('resolves active site employees for consumable issuance auto-fill', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $site = AssetSite::factory()->company($company)->custodyZone()->create();

    $custodian = createAssetEmployee($company);
    $holder = createAssetEmployee($company);
    $inactive = createAssetEmployee($company, ['is_active' => false]);

    $site->update(['default_custodian_employee_id' => $custodian->id]);

    Asset::factory()->company($company)->heldByEmployee($holder, $site)->create();

    $employeeIds = IssueAssetAction::siteActiveEmployeeIds($site->id, $company->id);

    expect($employeeIds)->toContain($custodian->id)
        ->and($employeeIds)->toContain($holder->id)
        ->and($employeeIds)->not->toContain($inactive->id);
});

it('auto-fills consumable recipients from selected departments and applies the default quantity', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);
    $department = Department::factory()->create(['company_id' => $company->id]);

    $first = createAssetEmployee($company, ['department_id' => $department->id]);
    $second = createAssetEmployee($company, ['department_id' => $department->id]);
    $inactive = createAssetEmployee($company, ['department_id' => $department->id, 'is_active' => false]);
    $outsider = createAssetEmployee($company);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 50,
    ]);

    $employeeIds = IssueAssetAction::departmentActiveEmployeeIds([$department->id], $company->id);

    expect($employeeIds)->toEqualCanonicalizing([$first->id, $second->id]);
    expect($employeeIds)->not->toContain($inactive->id)
        ->not->toContain($outsider->id);

    app(AssetSettings::class)->fill(['default_consumable_issue_quantity' => 2])->save();

    Livewire::test(ListAssets::class)
        ->callTableAction('issue', $batch, data: [
            'department_ids' => [$department->id],
            'issued_at'      => now(),
        ])
        ->assertHasNoTableActionErrors();

    $batch->refresh();

    expect((float) $batch->quantity)->toBe(46.0)
        ->and($batch->movements()->where('type', MovementType::Issued)->count())->toBe(2)
        ->and((float) $batch->movements()->where('type', MovementType::Issued)->sum('quantity'))->toBe(4.0);
});

it('auto-fills consumable recipients from the selected site when the setting is enabled', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $site = AssetSite::factory()->company($company)->custodyZone()->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);
    $assetProduct = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Asset,
    ]);
    $holder = createAssetEmployee($company);

    Asset::factory()->forProduct($assetProduct)->company($company)->heldByEmployee($holder, $site)->create();

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    Livewire::test(ListAssets::class)
        ->callTableAction('issue', $batch, data: [
            'site_id'   => $site->id,
            'issued_at' => now(),
        ])
        ->assertHasNoTableActionErrors();

    $batch->refresh();

    $issued = $batch->movements()->where('type', MovementType::Issued)->get();

    expect((float) $batch->quantity)->toBe(9.0)
        ->and($issued)->toHaveCount(1)
        ->and($issued->first()->to_employee_id)->toBe($holder->id);
});

it('does not auto-fill consumable recipients from the site when the setting is disabled', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $site = AssetSite::factory()->company($company)->custodyZone()->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);
    $assetProduct = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Asset,
    ]);
    $holder = createAssetEmployee($company);

    Asset::factory()->forProduct($assetProduct)->company($company)->heldByEmployee($holder, $site)->create();

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    app(AssetSettings::class)->fill(['auto_fill_consumable_employees_by_site' => false])->save();

    Livewire::test(ListAssets::class)
        ->callTableAction('issue', $batch, data: [
            'site_id'   => $site->id,
            'issued_at' => now(),
        ])
        ->assertHasTableActionErrors(['employee_ids']);

    expect((float) $batch->fresh()->quantity)->toBe(10.0);
});

it('restricts multiple employee recipients to consumables when batch issuance is enabled', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);
    $assetProduct = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Asset,
    ]);

    $fixedAsset = Asset::factory()->forProduct($assetProduct)->company($company)->create(['warehouse_id' => $warehouse->id]);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    Livewire::test(ListAssets::class)
        ->mountTableAction('issue', $fixedAsset)
        ->assertFormFieldIsVisible('employee_id')
        ->assertFormFieldIsHidden('employee_ids')
        ->assertFormFieldIsHidden('department_ids');

    Livewire::test(ListAssets::class)
        ->mountTableAction('issue', $batch)
        ->assertFormFieldIsHidden('employee_id')
        ->assertFormFieldIsVisible('employee_ids')
        ->assertFormFieldIsVisible('department_ids');
});

it('falls back to a single employee field for consumables when batch issuance is disabled', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    app(AssetSettings::class)->fill(['enable_consumables_batch_issue' => false])->save();

    Livewire::test(ListAssets::class)
        ->mountTableAction('issue', $batch)
        ->assertFormFieldIsVisible('employee_id')
        ->assertFormFieldIsHidden('employee_ids')
        ->assertFormFieldIsHidden('department_ids');
});

it('registers the scrollable recipients container and applies it to the recipients select', function () {
    $providerSource = file_get_contents((new ReflectionClass(AssetServiceProvider::class))->getFileName());

    expect($providerSource)->toContain('.fi-fo-select-scrollable .fi-select-input-value-badges-ctn');

    FilamentHelper::actingAs([
        'view_any_asset_asset',
        'update_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    Livewire::test(ListAssets::class)
        ->mountTableAction('issue', $batch)
        ->tap(function ($component): void {
            $field = $component->instance()->mountedActionSchema0->getFlatFields(withHidden: true)['employee_ids'];

            expect($field->getExtraFieldWrapperAttributes())->toMatchArray(['class' => 'fi-fo-select-scrollable'])
                ->and($field->getExtraAttributes())->toMatchArray(['class' => 'fi-fo-select-scrollable']);
        });
});
