<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

function makeGuardProduct(): AssetProduct
{
    return AssetProduct::factory()->create();
}

function makeGuardEmployee(): Employee
{
    return Employee::query()->first() ?? Employee::factory()->create(['is_active' => true]);
}

it('blocks soft and force deleting an asset while it is held', function () {
    $asset = Asset::factory()->heldByEmployee()->create();

    expect(fn () => $asset->delete())->toThrow(ValidationException::class)
        ->and($asset->fresh()->trashed())->toBeFalse();

    expect(fn () => $asset->forceDelete())->toThrow(ValidationException::class)
        ->and($asset->fresh())->not->toBeNull();
});

it('allows deleting an available asset without operational history and cleans up initial registration movement on force delete', function () {
    $available = Asset::factory()->create();
    $available->delete();

    expect($available->fresh()->trashed())->toBeTrue();

    $available->restore();
    expect($available->fresh()->trashed())->toBeFalse();

    $available->forceDelete();
    expect(Asset::withTrashed()->find($available->id))->toBeNull()
        ->and(AssetMovement::where('asset_id', $available->id)->exists())->toBeFalse();
});

it('blocks soft deleting an available asset with history, but allows soft deleting once terminated, while blocking force delete', function () {
    $assetWithHistory = Asset::factory()->create();

    AssetMovement::factory()->create([
        'company_id'       => $assetWithHistory->company_id,
        'asset_id'         => $assetWithHistory->id,
        'type'             => MovementType::Issued,
        'to_employee_id'   => makeGuardEmployee()->id,
        'condition'        => AssetCondition::Good,
    ]);

    expect(fn () => $assetWithHistory->delete())->toThrow(ValidationException::class)
        ->and($assetWithHistory->fresh()->trashed())->toBeFalse();

    $assetWithHistory->update(['status' => AssetStatus::Scrapped]);
    $assetWithHistory->delete();
    expect($assetWithHistory->fresh()->trashed())->toBeTrue();

    expect(fn () => $assetWithHistory->forceDelete())->toThrow(ValidationException::class)
        ->and($assetWithHistory->fresh())->not->toBeNull();
});

it('blocks soft deleting a product referenced by active assets, allows archiving when assets are terminated or trashed, and blocks force delete while referenced', function () {
    $product = makeGuardProduct();
    $asset = Asset::factory()->forProduct($product)->create(['status' => AssetStatus::Available]);

    expect(fn () => $product->delete())->toThrow(ValidationException::class);

    $asset->update(['status' => AssetStatus::Scrapped]);
    $asset->delete();

    expect($product->delete())->toBeTrue()
        ->and($product->fresh()->trashed())->toBeTrue();

    expect(fn () => $product->forceDelete())->toThrow(ValidationException::class);

    AssetMovement::where('asset_id', $asset->id)->delete();
    $asset->forceDelete();
    expect($product->forceDelete())->toBeTrue()
        ->and(AssetProduct::withTrashed()->find($product->id))->toBeNull();
});

it('blocks deactivating a product referenced by available or held assets', function () {
    $product = makeGuardProduct();
    Asset::factory()->forProduct($product)->create(['status' => AssetStatus::Available]);

    expect(fn () => $product->update(['is_active' => false]))->toThrow(ValidationException::class);
});

it('blocks deleting categories referenced by children or products', function () {
    $parent = AssetCategory::factory()->create();
    $child = AssetCategory::factory()->withParent($parent)->create();

    expect(fn () => $parent->delete())->toThrow(ValidationException::class);

    $child->delete();
    expect($child->fresh())->toBeNull();

    $product = AssetProduct::factory()->create(['category_id' => $parent->id]);
    expect(fn () => $parent->delete())->toThrow(ValidationException::class);
});

it('allows deleting an unreferenced product', function () {
    $product = makeGuardProduct();

    expect($product->delete())->toBeTrue()
        ->and($product->fresh()->trashed())->toBeTrue();
});

it('blocks deleting an employee with open custody', function () {
    $holder = makeGuardEmployee();
    Asset::factory()->heldByEmployee($holder)->create();

    expect(fn () => $holder->delete())->toThrow(ValidationException::class);
});

it('allows archiving an employee once custody is returned', function () {
    $employee = Employee::factory()->create(['user_id' => User::factory(), 'is_active' => true]);
    $asset = Asset::factory()->heldByEmployee($employee)->create();

    $asset->update([
        'status'              => AssetStatus::Available,
        'warehouse_id'        => AssetWarehouse::factory()->company($asset->company)->create()->id,
        'current_employee_id' => null,
    ]);

    $employee->delete();

    expect($employee->fresh()->trashed())->toBeTrue();
});

it('blocks deactivating a site with open custody and allows it once returned', function () {
    $site = AssetSite::factory()->custodyZone()->create();
    $employee = Employee::factory()->create(['company_id' => $site->company_id, 'user_id' => User::factory(), 'is_active' => true]);
    $asset = Asset::factory()->company($site->company)->heldByEmployee($employee, $site)->create();

    expect(fn () => $site->update(['is_active' => false]))->toThrow(ValidationException::class)
        ->and($site->fresh()->is_active)->toBeTrue();

    $asset->update([
        'status'          => AssetStatus::Available,
        'warehouse_id'    => AssetWarehouse::factory()->company($site->company)->create()->id,
        'current_site_id' => null,
    ]);

    $site->update(['is_active' => false]);

    expect($site->fresh()->is_active)->toBeFalse();
});

it('blocks deleting sites referenced by active assets, allows archiving when empty, and blocks force deleting when history exists', function () {
    $site = AssetSite::factory()->custodyZone()->create();
    $employee = Employee::factory()->create(['company_id' => $site->company_id, 'user_id' => User::factory(), 'is_active' => true]);
    $asset = Asset::factory()->company($site->company)->heldByEmployee($employee, $site)->create();

    expect(fn () => $site->delete())->toThrow(ValidationException::class)
        ->and(fn () => $site->forceDelete())->toThrow(ValidationException::class);

    $asset->update([
        'status'          => AssetStatus::Available,
        'warehouse_id'    => AssetWarehouse::factory()->company($site->company)->create()->id,
        'current_site_id' => null,
    ]);

    $site = $site->fresh();

    expect($site->delete())->toBeTrue()
        ->and($site->fresh()->trashed())->toBeTrue();

    expect(fn () => $site->forceDelete())->toThrow(ValidationException::class);
});

it('blocks changing company_id on an asset when held or with movement history', function () {
    $otherCompany = Company::factory()->create();

    $heldAsset = Asset::factory()->heldByEmployee()->create();
    expect(fn () => $heldAsset->update(['company_id' => $otherCompany->id]))
        ->toThrow(ValidationException::class);

    $historyAsset = Asset::factory()->create();
    AssetMovement::factory()->create([
        'company_id'     => $historyAsset->company_id,
        'asset_id'       => $historyAsset->id,
        'type'           => MovementType::Issued,
        'to_employee_id' => makeGuardEmployee()->id,
    ]);

    expect(fn () => $historyAsset->update(['company_id' => $otherCompany->id]))
        ->toThrow(ValidationException::class);
});

it('blocks changing company_id on an asset site referenced by assets or movements', function () {
    $otherCompany = Company::factory()->create();

    $siteWithAsset = AssetSite::factory()->custodyZone()->create();
    $employee = Employee::factory()->create(['company_id' => $siteWithAsset->company_id, 'user_id' => User::factory(), 'is_active' => true]);
    Asset::factory()->company($siteWithAsset->company)->heldByEmployee($employee, $siteWithAsset)->create();

    expect(fn () => $siteWithAsset->update(['company_id' => $otherCompany->id]))
        ->toThrow(ValidationException::class);
});

it('blocks deactivating an employee while they have open custody', function () {
    $holder = Employee::factory()->create([
        'user_id'   => User::factory()->create()->id,
        'is_active' => true,
    ]);
    Asset::factory()->heldByEmployee($holder)->create();

    expect(fn () => $holder->update(['is_active' => false]))->toThrow(ValidationException::class)
        ->and($holder->fresh()->is_active)->toBeTrue();
});

it('blocks deleting and deactivating a warehouse when it holds assets, allows archiving when empty, and blocks force deleting when history exists', function () {
    $warehouse = AssetWarehouse::factory()->create();
    $asset = Asset::factory()->company($warehouse->company)->available($warehouse)->create();

    expect(fn () => $warehouse->update(['is_active' => false]))->toThrow(ValidationException::class)
        ->and($warehouse->fresh()->is_active)->toBeTrue();

    expect(fn () => $warehouse->delete())->toThrow(ValidationException::class);

    $asset->update(['warehouse_id' => null, 'status' => AssetStatus::Scrapped]);

    $warehouse->update(['is_active' => false]);
    expect($warehouse->fresh()->is_active)->toBeFalse();

    expect($warehouse->delete())->toBeTrue()
        ->and($warehouse->fresh()->trashed())->toBeTrue();

    expect(fn () => $warehouse->forceDelete())->toThrow(ValidationException::class);
});

it('blocks archiving an parent site while child is active, and automatically restores archived parent when child is restored', function () {
    $parent = AssetSite::factory()->create();
    $child = AssetSite::factory()->withParent($parent)->create();

    expect(fn () => $parent->delete())->toThrow(ValidationException::class);

    $child->delete();
    $parent->delete();

    expect($parent->fresh()->trashed())->toBeTrue()
        ->and($child->fresh()->trashed())->toBeTrue();

    $child->restore();

    expect($child->fresh()->trashed())->toBeFalse()
        ->and($parent->fresh()->trashed())->toBeFalse();
});

it('rejects using soft-deleted warehouses, sites, or products for asset operations even when is_active is true', function () {
    $company = Company::query()->firstOrFail();

    $warehouse = AssetWarehouse::factory()->company($company)->create(['is_active' => true]);
    $warehouse->delete();

    $site = AssetSite::factory()->company($company)->assetLocation()->create(['is_active' => true]);
    $site->delete();

    $product = AssetProduct::factory()->create(['company_id' => $company->id, 'is_active' => true]);
    $product->delete();

    expect(fn () => Asset::factory()->company($company)->create(['warehouse_id' => $warehouse->id]))
        ->toThrow(ValidationException::class);

    expect(fn () => Asset::factory()->company($company)->create([
        'status'              => AssetStatus::Held,
        'current_site_id'     => $site->id,
        'current_employee_id' => null,
        'warehouse_id'        => null,
    ]))->toThrow(ValidationException::class);

    expect(fn () => Asset::factory()->company($company)->create(['product_id' => $product->id]))
        ->toThrow(ValidationException::class);
});

it('deactivates entire site subtree when root site is deactivated without open custody', function () {
    $parent = AssetSite::factory()->create();
    $child = AssetSite::factory()->withParent($parent)->create();
    $grandchild = AssetSite::factory()->withParent($child)->create();

    $parent->update(['is_active' => false]);

    expect($parent->fresh()->is_active)->toBeFalse()
        ->and($child->fresh()->is_active)->toBeFalse()
        ->and($grandchild->fresh()->is_active)->toBeFalse();
});

it('blocks deactivating a root site if any site in its subtree has open custody', function () {
    $parent = AssetSite::factory()->custodyZone()->create();
    $child = AssetSite::factory()->withParent($parent)->create();
    $employee = Employee::factory()->create(['company_id' => $parent->company_id, 'user_id' => User::factory(), 'is_active' => true]);
    Asset::factory()->company($parent->company)->heldByEmployee($employee, $child)->create();

    expect(fn () => $parent->update(['is_active' => false]))->toThrow(ValidationException::class)
        ->and($parent->fresh()->is_active)->toBeTrue()
        ->and($child->fresh()->is_active)->toBeTrue();
});

it('blocks changing usage_type on root site from AssetLocation to CustodyZone if any site in subtree holds fixed assets', function () {
    $parent = AssetSite::factory()->assetLocation()->create();
    $child = AssetSite::factory()->withParent($parent)->create();

    $fixedAsset = Asset::factory()->company($parent->company)->create([
        'status'              => AssetStatus::Held,
        'current_site_id'     => $child->id,
        'current_employee_id' => null,
        'warehouse_id'        => null,
    ]);

    expect(fn () => $parent->update(['usage_type' => AssetSiteUsage::CustodyZone]))
        ->toThrow(ValidationException::class);

    $fixedAsset->update([
        'status'          => AssetStatus::Available,
        'warehouse_id'    => AssetWarehouse::factory()->company($parent->company)->create()->id,
        'current_site_id' => null,
    ]);

    $parent->update(['usage_type' => AssetSiteUsage::CustodyZone]);

    expect($parent->fresh()->usage_type)->toBe(AssetSiteUsage::CustodyZone)
        ->and($child->fresh()->usage_type)->toBe(AssetSiteUsage::CustodyZone);
});

it('blocks changing usage_type on root site from CustodyZone to AssetLocation if any site in subtree holds employee custody assets', function () {
    $parent = AssetSite::factory()->custodyZone()->create();
    $child = AssetSite::factory()->withParent($parent)->create();
    $employee = Employee::factory()->create(['company_id' => $parent->company_id, 'user_id' => User::factory(), 'is_active' => true]);

    $custodyAsset = Asset::factory()->company($parent->company)->heldByEmployee($employee, $child)->create();

    expect(fn () => $parent->update(['usage_type' => AssetSiteUsage::AssetLocation]))
        ->toThrow(ValidationException::class);

    $custodyAsset->update([
        'status'              => AssetStatus::Available,
        'warehouse_id'        => AssetWarehouse::factory()->company($parent->company)->create()->id,
        'current_site_id'     => null,
        'current_employee_id' => null,
    ]);

    $parent->update(['usage_type' => AssetSiteUsage::AssetLocation]);

    expect($parent->fresh()->usage_type)->toBe(AssetSiteUsage::AssetLocation)
        ->and($child->fresh()->usage_type)->toBe(AssetSiteUsage::AssetLocation);
});

it('allows deactivating or archiving an employee who is only default site custodian or warehouse custodian', function () {
    $company = Company::query()->firstOrFail();
    $employee = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);

    AssetSite::factory()->company($company)->create(['default_custodian_employee_id' => $employee->id]);
    AssetWarehouse::factory()->company($company)->create(['custodian_employee_id' => $employee->id]);

    $employee->update(['is_active' => false]);
    expect($employee->fresh()->is_active)->toBeFalse();

    $employee->delete();
    expect($employee->fresh()->trashed())->toBeTrue();
});

it('blocks changing company_id on a site when child sites exist', function () {
    $parent = AssetSite::factory()->create();
    AssetSite::factory()->withParent($parent)->create();

    $otherCompany = Company::factory()->create();

    expect(fn () => $parent->update(['company_id' => $otherCompany->id]))
        ->toThrow(ValidationException::class);
});

it('blocks force deleting an employee with historical asset movements while allowing soft deleting after custody return', function () {
    $company = Company::query()->firstOrFail();
    $employee = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $asset = Asset::factory()->company($company)->heldByEmployee($employee)->create();

    $asset->update([
        'status'              => AssetStatus::Available,
        'warehouse_id'        => $warehouse->id,
        'current_employee_id' => null,
    ]);

    AssetMovement::factory()->create([
        'company_id'       => $company->id,
        'asset_id'         => $asset->id,
        'type'             => MovementType::Returned,
        'from_employee_id' => $employee->id,
        'to_warehouse_id'  => $warehouse->id,
        'condition'        => AssetCondition::Good,
    ]);

    $employee->delete();
    expect($employee->fresh()->trashed())->toBeTrue();

    expect(fn () => $employee->forceDelete())->toThrow(ValidationException::class);
});

it('blocks updating or deleting an asset movement directly to guarantee immutability', function () {
    $company = Company::query()->firstOrFail();
    $asset = Asset::factory()->company($company)->create();
    $movement = AssetMovement::factory()->create([
        'company_id' => $company->id,
        'asset_id'   => $asset->id,
        'type'       => MovementType::Registered,
        'condition'  => AssetCondition::New,
    ]);

    expect(fn () => $movement->update(['note' => 'tampered']))
        ->toThrow(ValidationException::class);

    expect(fn () => $movement->delete())
        ->toThrow(ValidationException::class);
});

it('rejects issuing custody on a trashed asset', function () {
    $service = app(AssetCustodyService::class);
    $asset = Asset::factory()->create();
    $asset->delete();
    expect($asset->fresh()->trashed())->toBeTrue();

    $employee = makeGuardEmployee();

    expect(fn () => $service->issue($asset, [
        'allocation_mode' => 'employee_custody',
        'employee_id'     => $employee->id,
    ]))->toThrow(ModelNotFoundException::class);
});
