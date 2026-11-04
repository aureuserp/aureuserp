<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Exceptions\CrossCompanyException;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('seeds the 5 default shared site types with null company_id', function () {
    $defaultNames = ['Office', 'Server Room', 'Workshop', 'Field', 'Vehicle'];

    $types = AssetSiteType::withoutGlobalScopes()->whereNull('company_id')->pluck('name')->all();

    foreach ($defaultNames as $name) {
        expect($types)->toContain($name);
    }
});

it('creates assets with unique tags and serial numbers per company and assigns warehouse', function () {
    $company = Company::factory()->create();
    $warehouse = AssetWarehouse::factory()->company($company)->create();

    $first = Asset::factory()->company($company)->create([
        'warehouse_id' => $warehouse->id,
        'asset_tag'    => 'AST-00001',
        'serial_no'    => 'SN-1',
    ]);
    $second = Asset::factory()->company($company)->create([
        'warehouse_id' => $warehouse->id,
        'asset_tag'    => 'AST-00002',
        'serial_no'    => 'SN-2',
    ]);

    expect($first->exists)->toBeTrue()
        ->and($first->status)->toBe(AssetStatus::Available)
        ->and($first->condition)->toBe(AssetCondition::New)
        ->and($first->warehouse_id)->toBe($warehouse->id)
        ->and($second->warehouse_id)->toBe($warehouse->id);
});

it('rejects duplicate asset tags and serial numbers within the same company', function () {
    $company = Company::factory()->create();

    Asset::factory()->company($company)->create(['asset_tag' => 'AST-00001', 'serial_no' => 'SN-1']);

    expect(fn () => Asset::factory()->company($company)->create(['asset_tag' => 'AST-00001', 'serial_no' => 'SN-9']))
        ->toThrow(QueryException::class);
});

it('rejects duplicate serial numbers even after the asset is soft deleted', function () {
    $company = Company::factory()->create();

    $asset = Asset::factory()->company($company)->create(['asset_tag' => 'AST-00001', 'serial_no' => 'SN-1']);
    $asset->delete();

    expect(fn () => Asset::factory()->company($company)->create(['asset_tag' => 'AST-00002', 'serial_no' => 'SN-1']))
        ->toThrow(QueryException::class);
});

it('allows multiple null serial numbers and the same serial in another company', function () {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    Asset::factory()->company($company)->create(['asset_tag' => 'AST-00001', 'serial_no' => null]);
    Asset::factory()->company($company)->create(['asset_tag' => 'AST-00002', 'serial_no' => null]);
    Asset::factory()->company($otherCompany)->create(['asset_tag' => 'AST-00001', 'serial_no' => 'SN-1']);
    Asset::factory()->company($company)->create(['asset_tag' => 'AST-00003', 'serial_no' => 'SN-1']);

    expect(Asset::withoutGlobalScopes()->count())->toBe(4);
});

it('enforces mandatory warehouse for available assets', function () {
    $company = Company::factory()->create();

    $asset = Asset::factory()->company($company)->make(['status' => AssetStatus::Available]);
    $asset->warehouse_id = null;

    expect(fn () => $asset->save())
        ->toThrow(ValidationException::class);
});

it('clears warehouse and requires employee when asset status is held', function () {
    $company = Company::factory()->create();
    $employee = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);

    $asset = Asset::factory()->company($company)->heldByEmployee($employee)->create();

    expect($asset->status)->toBe(AssetStatus::Held)
        ->and($asset->warehouse_id)->toBeNull()
        ->and($asset->current_employee_id)->toBe($employee->id);
});

it('clears warehouse and holder fields when asset is scrapped or lost', function () {
    $company = Company::factory()->create();

    $scrapped = Asset::factory()->company($company)->scrapped()->create();
    $lost = Asset::factory()->company($company)->lost()->create();

    expect($scrapped->warehouse_id)->toBeNull()
        ->and($scrapped->current_employee_id)->toBeNull()
        ->and($scrapped->current_site_id)->toBeNull()
        ->and($lost->warehouse_id)->toBeNull()
        ->and($lost->current_employee_id)->toBeNull()
        ->and($lost->current_site_id)->toBeNull();
});

it('automatically creates an initial registered movement on asset creation', function () {
    $company = Company::factory()->create();
    $warehouse = AssetWarehouse::factory()->company($company)->create();

    $asset = Asset::factory()->company($company)->create([
        'warehouse_id' => $warehouse->id,
        'note'         => 'Initial manual addition',
    ]);

    $movement = AssetMovement::where('asset_id', $asset->id)->first();

    expect($movement)->not->toBeNull()
        ->and($movement->type)->toBe(MovementType::Registered)
        ->and($movement->source_type)->toBe(AssetSourceType::Manual)
        ->and($movement->to_warehouse_id)->toBe($warehouse->id)
        ->and($movement->from_display)->toBe('Manual Entry')
        ->and($movement->to_display)->toBe($warehouse->name);
});

it('resolves model relationships and casts', function () {
    $company = Company::factory()->create();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create();
    $site = AssetSite::factory()->company($company)->custodyZone()->create();
    $employee = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);

    $asset = Asset::factory()->company($company)->forProduct($product)->heldByEmployee($employee, $site)->create();

    expect($asset->product)->not->toBeNull()
        ->and($asset->product->id)->toBe($product->id)
        ->and($asset->name)->toBe($product->name)
        ->and($asset->model)->toBe($product->model)
        ->and($asset->category)->toBe($product->category?->full_name)
        ->and($asset->currentSite->id)->toBe($site->id)
        ->and($asset->currentEmployee->id)->toBe($employee->id)
        ->and($asset->current_holder_name)->toBe($employee->name.' — '.$site->name);
});

it('stores default custodian on site and rejects cross-company default custodian', function () {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory(),
    ]);
    $foreignEmployee = Employee::factory()->create([
        'company_id' => $otherCompany->id,
        'user_id'    => User::factory(),
    ]);

    $site = AssetSite::factory()->company($company)->create([
        'default_custodian_employee_id' => $employee->id,
    ]);

    expect($site->fresh()->default_custodian_employee_id)->toBe($employee->id)
        ->and($site->defaultCustodian->id)->toBe($employee->id);

    expect(fn () => AssetSite::factory()->company($company)->create([
        'default_custodian_employee_id' => $foreignEmployee->id,
    ]))->toThrow(CrossCompanyException::class);
});

it('casts attachments as arrays on asset movements', function () {
    $asset = Asset::factory()->create();

    $movement = AssetMovement::factory()->create([
        'company_id'  => $asset->company_id,
        'asset_id'    => $asset->id,
        'attachments' => ['assets/movements/doc1.pdf', 'assets/movements/photo1.jpg'],
    ]);

    expect($movement->fresh()->attachments)->toBe(['assets/movements/doc1.pdf', 'assets/movements/photo1.jpg']);
});

it('supports multi-level hierarchical asset categories and circular reference protection', function () {
    $parent = AssetCategory::factory()->create(['name' => 'Computers & IT']);
    $child = AssetCategory::factory()->withParent($parent)->create(['name' => 'Laptops']);
    $grandchild = AssetCategory::factory()->withParent($child)->create(['name' => 'MacBooks']);

    expect($parent->full_name)->toBe('Computers & IT')
        ->and($child->full_name)->toBe('Computers & IT / Laptops')
        ->and($child->parent_path)->toBe('/'.$parent->id.'/')
        ->and($grandchild->full_name)->toBe('Computers & IT / Laptops / MacBooks')
        ->and($grandchild->parent_path)->toBe('/'.$parent->id.'/'.$child->id.'/');

    expect(function () use ($parent, $grandchild) {
        $parent->parent_id = $grandchild->id;
        $parent->save();
    })->toThrow(ValidationException::class);
});

it('filters asset categories recursively using applySubtreeFilter', function () {
    $parent = AssetCategory::factory()->create(['name' => 'Electronics']);
    $child = AssetCategory::factory()->withParent($parent)->create(['name' => 'Smartphones']);
    $other = AssetCategory::factory()->create(['name' => 'Furniture']);

    $company = Company::factory()->create();
    $prod1 = AssetProduct::factory()->company($company)->create(['category_id' => $parent->id]);
    $prod2 = AssetProduct::factory()->company($company)->create(['category_id' => $child->id]);
    $prod3 = AssetProduct::factory()->company($company)->create(['category_id' => $other->id]);

    $query = AssetProduct::query();
    AssetCategory::applySubtreeFilter($query, $parent->id, 'category_id');
    $results = $query->pluck('id')->all();

    expect($results)->toContain($prod1->id)
        ->and($results)->toContain($prod2->id)
        ->and($results)->not->toContain($prod3->id);
});

it('supports multi-level hierarchical asset sites, uniform usage propagation, and circular reference protection', function () {
    $company = Company::factory()->create();

    $root = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'HQ']);
    $child = AssetSite::factory()->company($company)->withParent($root)->create(['name' => 'Building A']);
    $grandchild = AssetSite::factory()->company($company)->withParent($child)->create(['name' => 'Server Room 1']);

    expect($root->full_name)->toBe('HQ')
        ->and($child->full_name)->toBe('HQ / Building A')
        ->and($child->parent_path)->toBe('/'.$root->id.'/')
        ->and($child->usage_type)->toBe(AssetSiteUsage::AssetLocation)
        ->and($grandchild->full_name)->toBe('HQ / Building A / Server Room 1')
        ->and($grandchild->parent_path)->toBe('/'.$root->id.'/'.$child->id.'/')
        ->and($grandchild->usage_type)->toBe(AssetSiteUsage::AssetLocation);

    expect(function () use ($root, $grandchild) {
        $root->parent_id = $grandchild->id;
        $root->save();
    })->toThrow(ValidationException::class);

    $conflictingChild = AssetSite::factory()->company($company)->custodyZone()->create([
        'name'      => 'Conflicting Child',
        'parent_id' => $root->id,
    ]);
    expect($conflictingChild->usage_type)->toBe(AssetSiteUsage::AssetLocation);

    $otherCompany = Company::factory()->create();
    expect(function () use ($root, $otherCompany) {
        AssetSite::factory()->company($otherCompany)->create([
            'name'      => 'Foreign Child',
            'parent_id' => $root->id,
        ]);
    })->toThrow(CrossCompanyException::class);
});

it('supports direct placement creation of held fixed site assets and employee custody', function () {
    $company = Company::factory()->create();
    $siteLocation = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Data Center']);
    $custodyZone = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'Sales Office']);
    $employee = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);

    $fixedAsset = Asset::factory()->company($company)->create([
        'status'              => AssetStatus::Held,
        'current_site_id'     => $siteLocation->id,
        'current_employee_id' => null,
        'warehouse_id'        => null,
    ]);

    expect($fixedAsset->status)->toBe(AssetStatus::Held)
        ->and($fixedAsset->warehouse_id)->toBeNull()
        ->and($fixedAsset->current_site_id)->toBe($siteLocation->id)
        ->and($fixedAsset->current_employee_id)->toBeNull()
        ->and($fixedAsset->current_holder_name)->toBe($siteLocation->name);

    $fixedMovement = AssetMovement::where('asset_id', $fixedAsset->id)->first();
    expect($fixedMovement)->not->toBeNull()
        ->and($fixedMovement->type)->toBe(MovementType::Registered)
        ->and($fixedMovement->to_site_id)->toBe($siteLocation->id)
        ->and($fixedMovement->to_employee_id)->toBeNull();

    $empAsset = Asset::factory()->company($company)->create([
        'status'              => AssetStatus::Held,
        'current_site_id'     => $custodyZone->id,
        'current_employee_id' => $employee->id,
        'warehouse_id'        => null,
    ]);

    expect($empAsset->status)->toBe(AssetStatus::Held)
        ->and($empAsset->current_employee_id)->toBe($employee->id)
        ->and($empAsset->current_site_id)->toBe($custodyZone->id)
        ->and($empAsset->current_holder_name)->toBe($employee->name.' — '.$custodyZone->name);

    $empMovement = AssetMovement::where('asset_id', $empAsset->id)->first();
    expect($empMovement)->not->toBeNull()
        ->and($empMovement->type)->toBe(MovementType::Registered)
        ->and($empMovement->to_employee_id)->toBe($employee->id)
        ->and($empMovement->to_site_id)->toBe($custodyZone->id);

    expect(function () use ($company, $custodyZone) {
        Asset::factory()->company($company)->create([
            'status'              => AssetStatus::Held,
            'current_site_id'     => $custodyZone->id,
            'current_employee_id' => null,
            'warehouse_id'        => null,
        ]);
    })->toThrow(ValidationException::class);

    expect(function () use ($company, $siteLocation, $employee) {
        Asset::factory()->company($company)->create([
            'status'              => AssetStatus::Held,
            'current_site_id'     => $siteLocation->id,
            'current_employee_id' => $employee->id,
            'warehouse_id'        => null,
        ]);
    })->toThrow(ValidationException::class);
});

it('adds product type and batch quantity columns to existing asset tables', function () {
    expect(Schema::hasColumn('asset_products', 'type'))->toBeTrue()
        ->and(Schema::hasColumn('assets', 'initial_quantity'))->toBeTrue()
        ->and(Schema::hasColumn('assets', 'quantity'))->toBeTrue()
        ->and(Schema::hasColumn('asset_movements', 'quantity'))->toBeTrue();
});
