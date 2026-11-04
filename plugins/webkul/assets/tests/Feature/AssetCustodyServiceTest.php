<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Exceptions\CrossCompanyException;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');

    custodyResetSettings();
});

afterEach(function () {
    custodyResetSettings();
});

function custodyService(): AssetCustodyService
{
    return app(AssetCustodyService::class);
}

function custodyResetSettings(): void
{
    $settings = app(AssetSettings::class);

    $settings->tag_prefix = 'AST-';
    $settings->auto_generate_tag = true;
    $settings->tag_padding = 5;
    $settings->default_condition = 'new';
    $settings->require_serial_by_default = false;
    $settings->default_warehouse_id = null;
    $settings->enable_site_assets = true;
    $settings->require_site_on_custody = false;
    $settings->default_custody_duration_days = null;
    $settings->require_expected_return_date = false;
    $settings->prevent_duplicate_product_custody = false;
    $settings->require_attachments_on_custody = false;
    $settings->require_attachments_on_damage = true;
    $settings->require_attachments_on_termination = false;
    $settings->restrict_termination_to_managers = false;
    $settings->termination_approval_cost_threshold = null;
    $settings->enable_consumables_batch_issue = true;
    $settings->auto_fill_consumable_employees_by_site = true;
    $settings->default_consumable_issue_quantity = 1;
    $settings->require_site_on_consumable_issue = false;
    $settings->require_attachments_on_consumable_issue = false;

    $settings->save();
}

function custodyCompany(): Company
{
    return Company::factory()->create();
}

function custodyWarehouse(Company $company, array $attributes = []): AssetWarehouse
{
    return AssetWarehouse::factory()->company($company)->create($attributes);
}

function custodyEmployee(Company $company, array $attributes = []): Employee
{
    return Employee::factory()->create([
        'company_id' => $company->id,
        'user_id'    => User::factory()->create()->id,
        'is_active'  => true,
        ...$attributes,
    ]);
}

function custodyProduct(Company $company, array $attributes = []): AssetProduct
{
    return AssetProduct::factory()->create([
        'company_id'  => $company->id,
        'category_id' => AssetCategory::query()->value('id') ?? AssetCategory::factory()->create(['company_id' => null])->id,
        ...$attributes,
    ]);
}

function custodySite(Company $company, array $attributes = []): AssetSite
{
    return AssetSite::factory()->company($company)->custodyZone()->create($attributes);
}

function custodyAsset(Company $company, array $attributes = []): Asset
{
    return Asset::factory()->company($company)->create($attributes);
}

function custodyExpectQueryViolation(Closure $callback): void
{
    expect(fn () => DB::transaction($callback))->toThrow(QueryException::class);
}

it('issues an available asset to an employee and rejects a double issue', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $employee = custodyEmployee($company);
    $asset = custodyAsset($company, ['warehouse_id' => $warehouse->id, 'condition' => AssetCondition::Good]);

    $movement = custodyService()->issue($asset, [
        'employee_id' => $employee->id,
        'note'        => 'Laptop bag included',
    ]);

    expect($movement->type)->toBe(MovementType::Issued)
        ->and($movement->from_warehouse_id)->toBe($warehouse->id)
        ->and($movement->to_employee_id)->toBe($employee->id)
        ->and($movement->to_site_id)->toBeNull()
        ->and($movement->condition)->toBe(AssetCondition::Good);

    $asset->refresh();

    expect($asset->status)->toBe(AssetStatus::Held)
        ->and($asset->warehouse_id)->toBeNull()
        ->and($asset->current_employee_id)->toBe($employee->id)
        ->and($asset->current_site_id)->toBeNull()
        ->and($asset->condition)->toBe(AssetCondition::Good)
        ->and($asset->messages()->count())->toBeGreaterThan(0);

    expect(fn () => custodyService()->issue($asset->fresh(), [
        'employee_id' => $employee->id,
    ]))->toThrow(ValidationException::class);
});

it('locks the asset row before issuing and rejects a stale in-memory copy', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $employee = custodyEmployee($company);
    $asset = custodyAsset($company, ['warehouse_id' => $warehouse->id]);

    $staleAssetA = Asset::find($asset->id);
    $staleAssetB = Asset::find($asset->id);

    expect($staleAssetA->status)->toBe(AssetStatus::Available)
        ->and($staleAssetB->status)->toBe(AssetStatus::Available);

    DB::enableQueryLog();

    custodyService()->issue($staleAssetA, [
        'employee_id' => $employee->id,
    ]);

    $lockedForUpdate = collect(DB::getQueryLog())
        ->pluck('query')
        ->contains(fn (string $query): bool => str_contains(strtolower($query), 'for update'));

    DB::disableQueryLog();

    expect($lockedForUpdate)->toBeTrue()
        ->and($asset->fresh()->status)->toBe(AssetStatus::Held);

    expect(fn () => custodyService()->issue($staleAssetB, [
        'employee_id' => $employee->id,
    ]))->toThrow(ValidationException::class);

    expect(AssetMovement::where('asset_id', $asset->id)->where('type', MovementType::Issued)->count())->toBe(1);
});

it('issues an asset with employee and deployment site', function () {
    $company = custodyCompany();
    $employee = custodyEmployee($company);
    $site = custodySite($company);
    $asset = custodyAsset($company);

    $movement = custodyService()->issue($asset, [
        'employee_id' => $employee->id,
        'site_id'     => $site->id,
    ]);

    expect($movement->to_site_id)->toBe($site->id)
        ->and($movement->to_employee_id)->toBe($employee->id);

    $asset->refresh();

    expect($asset->status)->toBe(AssetStatus::Held)
        ->and($asset->warehouse_id)->toBeNull()
        ->and($asset->current_site_id)->toBe($site->id)
        ->and($asset->current_employee_id)->toBe($employee->id)
        ->and($asset->current_holder_name)->toBe($employee->name.' — '.$site->name);
});

it('enforces site on custody when require_site_on_custody is enabled', function () {
    $company = custodyCompany();
    $employee = custodyEmployee($company);
    $site = custodySite($company);
    $asset = custodyAsset($company);

    $settings = app(AssetSettings::class);
    $settings->require_site_on_custody = true;
    $settings->save();

    expect(fn () => custodyService()->issue($asset, [
        'employee_id' => $employee->id,
    ]))->toThrow(ValidationException::class);

    $movement = custodyService()->issue($asset->fresh(), [
        'employee_id' => $employee->id,
        'site_id'     => $site->id,
    ]);

    expect($movement->to_site_id)->toBe($site->id);
});

it('transfers custody across employees and sites and rejects same-holder transfers', function () {
    $company = custodyCompany();
    $firstEmployee = custodyEmployee($company);
    $secondEmployee = custodyEmployee($company);
    $firstSite = custodySite($company);
    $secondSite = custodySite($company);
    $asset = custodyAsset($company, ['condition' => AssetCondition::New]);

    custodyService()->issue($asset, [
        'employee_id' => $firstEmployee->id,
        'site_id'     => $firstSite->id,
    ]);

    $transfer1 = custodyService()->transfer($asset->fresh(), [
        'employee_id'  => $secondEmployee->id,
        'site_id'      => $secondSite->id,
        'condition_in' => AssetCondition::Fair,
    ]);

    expect($transfer1->type)->toBe(MovementType::Transferred)
        ->and($transfer1->from_employee_id)->toBe($firstEmployee->id)
        ->and($transfer1->from_site_id)->toBe($firstSite->id)
        ->and($transfer1->to_employee_id)->toBe($secondEmployee->id)
        ->and($transfer1->to_site_id)->toBe($secondSite->id)
        ->and($transfer1->condition)->toBe(AssetCondition::Fair);

    expect($asset->fresh()->current_employee_id)->toBe($secondEmployee->id)
        ->and($asset->fresh()->current_site_id)->toBe($secondSite->id)
        ->and($asset->fresh()->condition)->toBe(AssetCondition::Fair);

    expect(fn () => custodyService()->transfer($asset->fresh(), [
        'employee_id' => $secondEmployee->id,
        'site_id'     => $secondSite->id,
    ]))->toThrow(ValidationException::class);

    $transfer2 = custodyService()->transfer($asset->fresh(), [
        'employee_id' => $secondEmployee->id,
        'site_id'     => $firstSite->id,
    ]);
    expect($transfer2->to_site_id)->toBe($firstSite->id);

    expect(AssetMovement::where('asset_id', $asset->id)->count())->toBe(4);
});

it('returns held asset to an active warehouse with condition continuity', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $employee = custodyEmployee($company);
    $site = custodySite($company);

    $asset = custodyAsset($company, ['condition' => AssetCondition::Good]);

    custodyService()->issue($asset, [
        'employee_id' => $employee->id,
        'site_id'     => $site->id,
    ]);

    expect(fn () => custodyService()->return($asset->fresh(), [
        'warehouse_id' => $warehouse->id,
        'condition_in' => AssetCondition::Damaged,
    ]))->toThrow(ValidationException::class);

    $returnedMovement = custodyService()->return($asset->fresh(), [
        'warehouse_id' => $warehouse->id,
        'condition_in' => AssetCondition::Damaged,
        'note'         => 'Cracked screen during transit',
        'attachments'  => ['photos/screen.jpg'],
    ]);

    expect($returnedMovement->type)->toBe(MovementType::Returned)
        ->and($returnedMovement->from_employee_id)->toBe($employee->id)
        ->and($returnedMovement->from_site_id)->toBe($site->id)
        ->and($returnedMovement->to_warehouse_id)->toBe($warehouse->id)
        ->and($returnedMovement->condition)->toBe(AssetCondition::Damaged);

    $asset->refresh();

    expect($asset->status)->toBe(AssetStatus::Available)
        ->and($asset->condition)->toBe(AssetCondition::Damaged)
        ->and($asset->warehouse_id)->toBe($warehouse->id)
        ->and($asset->current_employee_id)->toBeNull()
        ->and($asset->current_site_id)->toBeNull();

    expect(fn () => custodyService()->return($asset->fresh(), [
        'warehouse_id' => $warehouse->id,
    ]))->toThrow(ValidationException::class);
});

it('scraps and marks assets lost from held and available states', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $employee = custodyEmployee($company);

    $scrapped = custodyAsset($company, ['warehouse_id' => $warehouse->id]);
    custodyService()->issue($scrapped, [
        'employee_id' => $employee->id,
    ]);

    $scrapMovement = custodyService()->scrap($scrapped->fresh(), [
        'note'        => 'Device burned out',
        'attachments' => ['photos/burned.jpg'],
    ]);

    expect($scrapMovement->type)->toBe(MovementType::Scrapped)
        ->and($scrapMovement->from_employee_id)->toBe($employee->id)
        ->and($scrapped->fresh()->status)->toBe(AssetStatus::Scrapped)
        ->and($scrapped->fresh()->warehouse_id)->toBeNull()
        ->and($scrapped->fresh()->current_employee_id)->toBeNull();

    $lost = custodyAsset($company, ['warehouse_id' => $warehouse->id]);
    custodyService()->issue($lost, [
        'employee_id' => $employee->id,
    ]);

    $lostMovement = custodyService()->markLost($lost->fresh(), ['note' => 'Stolen from car']);

    expect($lostMovement->type)->toBe(MovementType::Lost)
        ->and($lost->fresh()->status)->toBe(AssetStatus::Lost)
        ->and($lost->fresh()->warehouse_id)->toBeNull()
        ->and($lost->fresh()->current_employee_id)->toBeNull();

    $available = custodyAsset($company, ['warehouse_id' => $warehouse->id]);
    $scrapAvail = custodyService()->scrap($available, [
        'attachments' => ['photos/old.jpg'],
    ]);

    expect($scrapAvail->type)->toBe(MovementType::Scrapped)
        ->and($scrapAvail->from_warehouse_id)->toBe($warehouse->id)
        ->and($available->fresh()->status)->toBe(AssetStatus::Scrapped);

    expect(fn () => custodyService()->markLost($available->fresh()))->toThrow(ValidationException::class);
});

it('enforces attachment requirements when custody and damage settings are active', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $employee = custodyEmployee($company);
    $asset = custodyAsset($company, ['warehouse_id' => $warehouse->id]);

    $settings = app(AssetSettings::class);
    $settings->require_attachments_on_custody = true;
    $settings->save();

    expect(fn () => custodyService()->issue($asset, [
        'employee_id' => $employee->id,
    ]))->toThrow(ValidationException::class);

    $movement = custodyService()->issue($asset->fresh(), [
        'employee_id' => $employee->id,
        'attachments' => ['contracts/voucher.pdf'],
    ]);
    expect($movement->attachments)->toBe(['contracts/voucher.pdf']);

    $settings->require_attachments_on_custody = false;
    $settings->require_attachments_on_damage = true;
    $settings->save();

    expect(fn () => custodyService()->return($asset->fresh(), [
        'warehouse_id' => $warehouse->id,
        'condition_in' => AssetCondition::Damaged,
        'note'         => 'Broken chassis',
    ]))->toThrow(ValidationException::class);

    $returned = custodyService()->return($asset->fresh(), [
        'warehouse_id' => $warehouse->id,
        'condition_in' => AssetCondition::Damaged,
        'note'         => 'Broken chassis',
        'attachments'  => ['photos/broken_chassis.png'],
    ]);
    expect($returned->attachments)->toBe(['photos/broken_chassis.png']);
});

it('auto-fills product fields on creation without overwriting explicit values', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company, [
        'name'  => 'iPad Gen 8',
        'model' => 'A2270',
        'cost'  => 1450,
    ]);

    $asset = Asset::query()->create([
        'company_id'   => $company->id,
        'warehouse_id' => $warehouse->id,
        'product_id'   => $product->id,
        'serial_no'    => 'SN-IPAD-1',
    ]);

    expect($asset->asset_tag)->toBe('AST-00001')
        ->and($asset->name)->toBe('iPad Gen 8')
        ->and($asset->model)->toBe('A2270')
        ->and($asset->category)->toBe($product->category->full_name)
        ->and((float) $asset->cost)->toBe(1450.0)
        ->and($asset->condition)->toBe(AssetCondition::New);
});

it('rejects cross-company products, employees, sites, and warehouses', function () {
    $company = custodyCompany();
    $otherCompany = custodyCompany();
    $foreignProduct = custodyProduct($otherCompany);
    $foreignWarehouse = custodyWarehouse($otherCompany);
    $foreignEmployee = custodyEmployee($otherCompany);
    $foreignSite = custodySite($otherCompany);

    expect(fn () => Asset::query()->create([
        'company_id'   => $company->id,
        'warehouse_id' => $foreignWarehouse->id,
        'product_id'   => $foreignProduct->id,
        'asset_tag'    => 'CROSS-1',
        'serial_no'    => 'CROSS-1',
    ]))->toThrow(CrossCompanyException::class);

    $asset = custodyAsset($company);

    expect(fn () => custodyService()->issue($asset, [
        'employee_id' => $foreignEmployee->id,
    ]))->toThrow(CrossCompanyException::class);

    expect(fn () => custodyService()->issue($asset->fresh(), [
        'employee_id' => custodyEmployee($company)->id,
        'site_id'     => $foreignSite->id,
    ]))->toThrow(CrossCompanyException::class);
});

it('bulk registers serialized units with sequential tags into a warehouse', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company, ['name' => 'Tablet']);

    $assets = custodyService()->bulkRegister([
        'company_id'   => $company->id,
        'warehouse_id' => $warehouse->id,
        'product_id'   => $product->id,
        'items'        => [
            ['serial_no' => 'SER-1'],
            ['serial_no' => 'SER-2'],
            ['serial_no' => 'SER-3'],
        ],
    ]);

    expect($assets)->toHaveCount(3)
        ->and($assets->pluck('serial_no')->all())->toBe(['SER-1', 'SER-2', 'SER-3'])
        ->and($assets->pluck('asset_tag')->all())->toBe(['AST-00001', 'AST-00002', 'AST-00003'])
        ->and($assets->every(fn (Asset $a) => $a->warehouse_id === $warehouse->id))->toBeTrue()
        ->and($assets->every(fn (Asset $a) => $a->status === AssetStatus::Available))->toBeTrue();

    foreach ($assets as $asset) {
        $movement = AssetMovement::where('asset_id', $asset->id)->first();
        expect($movement->type)->toBe(MovementType::Registered)
            ->and($movement->source_type)->toBe(AssetSourceType::Bulk)
            ->and($movement->to_warehouse_id)->toBe($warehouse->id);
    }
});

it('validates chronological dates across custody operations', function () {
    $company = custodyCompany();
    $employee = custodyEmployee($company);
    $otherEmployee = custodyEmployee($company);
    $warehouse = custodyWarehouse($company);
    $asset = custodyAsset($company, ['warehouse_id' => $warehouse->id]);

    expect(fn () => custodyService()->issue($asset, [
        'employee_id'        => $employee->id,
        'issued_at'          => '2026-03-20 10:00:00',
        'expected_return_at' => '2026-03-19',
    ]))->toThrow(ValidationException::class);

    custodyService()->issue($asset, [
        'employee_id' => $employee->id,
        'issued_at'   => '2026-03-20 10:00:00',
    ]);

    expect(fn () => custodyService()->transfer($asset, [
        'employee_id' => $otherEmployee->id,
        'issued_at'   => '2026-03-19 10:00:00',
    ]))->toThrow(ValidationException::class);

    expect(fn () => custodyService()->return($asset, [
        'warehouse_id' => $warehouse->id,
        'ended_at'     => '2026-03-19 10:00:00',
    ]))->toThrow(ValidationException::class);

    expect(fn () => custodyService()->scrap($asset, [
        'ended_at' => '2026-03-19 10:00:00',
    ]))->toThrow(ValidationException::class);
});

it('rejects issuing or transferring custody to an inactive employee', function () {
    $company = custodyCompany();
    $activeEmployee = custodyEmployee($company);
    $inactiveEmployee = custodyEmployee($company, ['is_active' => false]);
    $asset = custodyAsset($company);

    expect(fn () => custodyService()->issue($asset, [
        'employee_id' => $inactiveEmployee->id,
    ]))->toThrow(ValidationException::class);

    custodyService()->issue($asset, [
        'employee_id' => $activeEmployee->id,
    ]);

    expect(fn () => custodyService()->transfer($asset, [
        'employee_id' => $inactiveEmployee->id,
    ]))->toThrow(ValidationException::class);
});

it('falls back to site default custodian when issuing with empty employee_id and specified site', function () {
    $company = custodyCompany();
    $custodian = custodyEmployee($company);
    $site = custodySite($company, ['default_custodian_employee_id' => $custodian->id]);
    $asset = custodyAsset($company);

    $movement = custodyService()->issue($asset, [
        'site_id' => $site->id,
    ]);

    expect($asset->fresh()->current_employee_id)->toBe($custodian->id)
        ->and($asset->fresh()->current_site_id)->toBe($site->id)
        ->and($movement->to_employee_id)->toBe($custodian->id);
});

it('issues custody to a site as a fixed asset and enforces AssetLocation site usage', function () {
    $company = custodyCompany();
    $locationSite = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Server Farm']);
    $custodySite = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'Call Center']);
    $asset = custodyAsset($company);

    expect(fn () => custodyService()->issue($asset, [
        'allocation_mode' => 'site_asset',
        'site_id'         => $custodySite->id,
    ]))->toThrow(ValidationException::class);

    $movement = custodyService()->issue($asset, [
        'allocation_mode'    => 'site_asset',
        'site_id'            => $locationSite->id,
        'expected_return_at' => '2026-12-31',
    ]);

    expect($asset->fresh()->status)->toBe(AssetStatus::Held)
        ->and($asset->fresh()->current_site_id)->toBe($locationSite->id)
        ->and($asset->fresh()->current_employee_id)->toBeNull()
        ->and($asset->fresh()->warehouse_id)->toBeNull()
        ->and($asset->fresh()->expected_return_at)->toBeNull()
        ->and($movement->type)->toBe(MovementType::Issued)
        ->and($movement->to_site_id)->toBe($locationSite->id)
        ->and($movement->to_employee_id)->toBeNull()
        ->and($movement->to_display)->toBe($locationSite->full_name);
});

it('rejects issuing employee custody to an AssetLocation site', function () {
    $company = custodyCompany();
    $employee = custodyEmployee($company);
    $locationSite = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Server Farm']);
    $asset = custodyAsset($company);

    expect(fn () => custodyService()->issue($asset, [
        'allocation_mode' => 'employee_custody',
        'employee_id'     => $employee->id,
        'site_id'         => $locationSite->id,
    ]))->toThrow(ValidationException::class);

    try {
        custodyService()->issue($asset, [
            'allocation_mode' => 'employee_custody',
            'employee_id'     => $employee->id,
            'site_id'         => $locationSite->id,
        ]);
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('site_id')
            ->and($e->errors()['site_id'][0])->toBe(__('assets::services/asset-custody.custody-requires-custody-zone-site'));
    }
});

it('transfers custody between site-only fixed assets and employee custody and rejects same-holder', function () {
    $company = custodyCompany();
    $siteA = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Location A']);
    $siteB = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'Location B']);
    $custodyZone = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'Custody Zone C']);
    $employee = custodyEmployee($company);

    $asset = custodyAsset($company);

    custodyService()->issue($asset, [
        'allocation_mode' => 'site_asset',
        'site_id'         => $siteA->id,
    ]);

    expect(fn () => custodyService()->transfer($asset->fresh(), [
        'allocation_mode' => 'site_asset',
        'site_id'         => $siteA->id,
    ]))->toThrow(ValidationException::class);

    $mov1 = custodyService()->transfer($asset->fresh(), [
        'allocation_mode' => 'site_asset',
        'site_id'         => $siteB->id,
    ]);

    expect($asset->fresh()->current_site_id)->toBe($siteB->id)
        ->and($asset->fresh()->current_employee_id)->toBeNull()
        ->and($mov1->from_site_id)->toBe($siteA->id)
        ->and($mov1->to_site_id)->toBe($siteB->id)
        ->and($mov1->from_display)->toBe($siteA->full_name)
        ->and($mov1->to_display)->toBe($siteB->full_name);

    $mov2 = custodyService()->transfer($asset->fresh(), [
        'allocation_mode' => 'employee_custody',
        'employee_id'     => $employee->id,
        'site_id'         => $custodyZone->id,
    ]);

    expect($asset->fresh()->current_employee_id)->toBe($employee->id)
        ->and($asset->fresh()->current_site_id)->toBe($custodyZone->id)
        ->and($mov2->from_site_id)->toBe($siteB->id)
        ->and($mov2->from_employee_id)->toBeNull()
        ->and($mov2->to_employee_id)->toBe($employee->id)
        ->and($mov2->to_site_id)->toBe($custodyZone->id);

    $mov3 = custodyService()->transfer($asset->fresh(), [
        'allocation_mode' => 'site_asset',
        'site_id'         => $siteA->id,
    ]);

    expect($asset->fresh()->current_employee_id)->toBeNull()
        ->and($asset->fresh()->current_site_id)->toBe($siteA->id)
        ->and($mov3->from_employee_id)->toBe($employee->id)
        ->and($mov3->from_site_id)->toBe($custodyZone->id)
        ->and($mov3->to_employee_id)->toBeNull()
        ->and($mov3->to_site_id)->toBe($siteA->id);
});

it('bulk registers asset units with site_asset and employee_custody initial placements', function () {
    $company = custodyCompany();
    $product = AssetProduct::factory()->company($company)->create();
    $siteLocation = AssetSite::factory()->company($company)->assetLocation()->create();
    $custodyZone = AssetSite::factory()->company($company)->custodyZone()->create();
    $employee = custodyEmployee($company);

    $fixedUnits = custodyService()->bulkRegister([
        'company_id'        => $company->id,
        'product_id'        => $product->id,
        'initial_placement' => 'site_asset',
        'site_id'           => $siteLocation->id,
        'tracking_mode'     => 'with_serials',
        'items'             => [
            ['serial_no' => 'FIXED-SN-001', 'asset_tag' => 'AST-FIXED-001', 'condition' => AssetCondition::New],
            ['serial_no' => 'FIXED-SN-002', 'asset_tag' => 'AST-FIXED-002', 'condition' => AssetCondition::New],
        ],
    ]);

    expect($fixedUnits)->toHaveCount(2);
    foreach ($fixedUnits as $unit) {
        expect($unit->status)->toBe(AssetStatus::Held)
            ->and($unit->warehouse_id)->toBeNull()
            ->and($unit->current_site_id)->toBe($siteLocation->id)
            ->and($unit->current_employee_id)->toBeNull();
    }

    $empUnits = custodyService()->bulkRegister([
        'company_id'        => $company->id,
        'product_id'        => $product->id,
        'initial_placement' => 'employee_custody',
        'employee_id'       => $employee->id,
        'site_id'           => $custodyZone->id,
        'tracking_mode'     => 'with_serials',
        'items'             => [
            ['serial_no' => 'EMP-SN-001', 'asset_tag' => 'AST-EMP-001', 'condition' => AssetCondition::New],
        ],
    ]);

    expect($empUnits)->toHaveCount(1)
        ->and($empUnits->first()->status)->toBe(AssetStatus::Held)
        ->and($empUnits->first()->current_employee_id)->toBe($employee->id)
        ->and($empUnits->first()->current_site_id)->toBe($custodyZone->id);
});

it('enforces require_serial_by_default when registering assets', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company);

    app(AssetSettings::class)->fill(['require_serial_by_default' => true])->save();

    // Direct creation without serial should fail
    expect(fn () => Asset::create([
        'company_id'   => $company->id,
        'product_id'   => $product->id,
        'warehouse_id' => $warehouse->id,
        'serial_no'    => null,
    ]))->toThrow(ValidationException::class);

    // Bulk registration without serials mode should fail
    expect(fn () => custodyService()->bulkRegister([
        'company_id'    => $company->id,
        'product_id'    => $product->id,
        'warehouse_id'  => $warehouse->id,
        'tracking_mode' => 'without_serials',
        'quantity'      => 2,
    ]))->toThrow(ValidationException::class);
});

it('enforces enable_site_assets setting for site-only allocations', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company);
    $siteLocation = AssetSite::factory()->company($company)->create(['usage_type' => AssetSiteUsage::AssetLocation]);

    $asset = Asset::factory()->company($company)->available($warehouse)->forProduct($product)->create();

    app(AssetSettings::class)->fill(['enable_site_assets' => false])->save();

    // Issue to site-only should fail
    expect(fn () => custodyService()->issue($asset, [
        'allocation_mode' => 'site_asset',
        'site_id'         => $siteLocation->id,
    ]))->toThrow(ValidationException::class);

    // Bulk register to site_asset should fail
    expect(fn () => custodyService()->bulkRegister([
        'company_id'        => $company->id,
        'product_id'        => $product->id,
        'initial_placement' => 'site_asset',
        'site_id'           => $siteLocation->id,
        'tracking_mode'     => 'with_serials',
        'items'             => [
            ['serial_no' => 'FIXED-SN-999', 'asset_tag' => 'AST-FIXED-999'],
        ],
    ]))->toThrow(ValidationException::class);
});

it('enforces require_expected_return_date on employee custody issue and transfer', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $employee1 = custodyEmployee($company);
    $employee2 = custodyEmployee($company);
    $custodyZone = custodySite($company);

    $asset = Asset::factory()->company($company)->available($warehouse)->create();

    app(AssetSettings::class)->fill(['require_expected_return_date' => true])->save();

    // Issue without expected return date should fail
    expect(fn () => custodyService()->issue($asset, [
        'employee_id' => $employee1->id,
        'site_id'     => $custodyZone->id,
    ]))->toThrow(ValidationException::class);

    // Issue with expected return date should pass
    custodyService()->issue($asset, [
        'employee_id'        => $employee1->id,
        'site_id'            => $custodyZone->id,
        'expected_return_at' => now()->addDays(7)->toDateString(),
    ]);

    expect($asset->refresh()->status)->toBe(AssetStatus::Held);

    // Transfer without expected return date should fail
    expect(fn () => custodyService()->transfer($asset, [
        'employee_id' => $employee2->id,
        'site_id'     => $custodyZone->id,
    ]))->toThrow(ValidationException::class);

    // Transfer with expected return date should pass
    custodyService()->transfer($asset, [
        'employee_id'        => $employee2->id,
        'site_id'            => $custodyZone->id,
        'expected_return_at' => now()->addDays(14)->toDateString(),
    ]);

    expect($asset->refresh()->current_employee_id)->toBe($employee2->id);
});

it('enforces prevent_duplicate_product_custody on issue, transfer, and bulk register', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company);
    $employee = custodyEmployee($company);
    $custodyZone = custodySite($company);

    $asset1 = Asset::factory()->company($company)->available($warehouse)->forProduct($product)->create();
    $asset2 = Asset::factory()->company($company)->available($warehouse)->forProduct($product)->create();

    app(AssetSettings::class)->fill(['prevent_duplicate_product_custody' => true])->save();

    custodyService()->issue($asset1, [
        'employee_id' => $employee->id,
        'site_id'     => $custodyZone->id,
    ]);

    // Issue second asset of same product to same employee should fail
    expect(fn () => custodyService()->issue($asset2, [
        'employee_id' => $employee->id,
        'site_id'     => $custodyZone->id,
    ]))->toThrow(ValidationException::class);

    // Bulk register multiple units of same product to employee should fail
    expect(fn () => custodyService()->bulkRegister([
        'company_id'        => $company->id,
        'product_id'        => $product->id,
        'initial_placement' => 'employee_custody',
        'employee_id'       => $employee->id,
        'site_id'           => $custodyZone->id,
        'tracking_mode'     => 'with_serials',
        'items'             => [
            ['serial_no' => 'DUP-SN-001', 'asset_tag' => 'AST-DUP-001'],
        ],
    ]))->toThrow(ValidationException::class);
});

it('enforces require_attachments_on_termination and restrict_termination_to_managers', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $asset = Asset::factory()->company($company)->available($warehouse)->create(['cost' => 1500]);

    app(AssetSettings::class)->fill([
        'require_attachments_on_termination' => true,
    ])->save();

    // Scrap without attachments should fail
    expect(fn () => custodyService()->scrap($asset, []))->toThrow(ValidationException::class);

    // Scrap with attachments should succeed
    custodyService()->scrap($asset, [
        'attachments' => ['scrap_doc.pdf'],
    ]);
    expect($asset->refresh()->status)->toBe(AssetStatus::Scrapped);

    // Test restrict_termination_to_managers
    $officerUser = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'delete_asset_asset',
    ], false);
    $officerUser->update(['resource_permission' => PermissionType::INDIVIDUAL->value]);

    $asset2 = Asset::factory()->company($company)->available($warehouse)->create(['cost' => 1500, 'creator_id' => $officerUser->id]);
    app(AssetSettings::class)->fill([
        'require_attachments_on_termination'   => false,
        'require_attachments_on_damage'        => false,
        'restrict_termination_to_managers'     => true,
        'termination_approval_cost_threshold'  => 1000,
    ])->save();

    // Individual permission user cannot scrap asset above threshold
    expect(fn () => custodyService()->scrap($asset2, []))->toThrow(ValidationException::class);

    // Global permission (manager) user can scrap
    $managerUser = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'delete_asset_asset',
    ], true);

    custodyService()->scrap($asset2, []);
    expect($asset2->refresh()->status)->toBe(AssetStatus::Scrapped);

    auth()->forgetGuards();
});

it('generates asset tags respecting tag_padding setting', function () {
    $company = custodyCompany();
    app(AssetSettings::class)->fill([
        'tag_prefix'  => 'PAD-',
        'tag_padding' => 6,
    ])->save();

    $tag = custodyService()->nextAssetTag($company->id);
    expect($tag)->toBe('PAD-000001');
});

it('persists initial_attachments into registered movement without SQL column error and merges transfer attachments', function () {
    $company = custodyCompany();
    $product = AssetProduct::factory()->company($company)->create();
    $emp1 = custodyEmployee($company);
    $emp2 = custodyEmployee($company);

    $asset = Asset::query()->create([
        'company_id'          => $company->id,
        'product_id'          => $product->id,
        'status'              => AssetStatus::Held,
        'current_employee_id' => $emp1->id,
        'initial_attachments' => ['assets/attachments/handover-initial.pdf'],
    ]);

    $regMovement = $asset->movements()->where('type', MovementType::Registered)->firstOrFail();
    expect($regMovement->attachments)->toBe(['assets/attachments/handover-initial.pdf']);

    $transferMovement = custodyService()->transfer($asset, [
        'allocation_mode'    => 'employee_custody',
        'employee_id'        => $emp2->id,
        'return_attachments' => ['assets/attachments/return-doc.pdf'],
        'issue_attachments'  => ['assets/attachments/issue-doc.pdf'],
    ]);

    expect($transferMovement->attachments)->toBe([
        'assets/attachments/return-doc.pdf',
        'assets/attachments/issue-doc.pdf',
    ]);
});

it('registers a consumable batch with initial quantity without requiring a serial number even when require_serial_by_default is enabled', function () {
    $company = Company::factory()->create();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
        'cost' => 15.50,
    ]);

    $settings = app(AssetSettings::class);
    $settings->require_serial_by_default = true;
    $settings->save();

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 100,
    ]);

    expect($batch->isConsumable())->toBeTrue()
        ->and((float) $batch->initial_quantity)->toBe(100.0)
        ->and((float) $batch->quantity)->toBe(100.0)
        ->and($batch->serial_no)->toBeNull()
        ->and($batch->status)->toBe(AssetStatus::Available);

    $movement = $batch->movements()->first();
    expect($movement->type)->toBe(MovementType::Registered)
        ->and((float) $movement->quantity)->toBe(100.0);
});

it('prevents changing product type once asset or batch records exist', function () {
    $company = Company::factory()->create();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
    ]);

    Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 25,
    ]);

    expect(fn () => $product->update(['type' => AssetProductType::Asset]))
        ->toThrow(ValidationException::class);
});

it('issues consumable quantities to multiple employees in one batch operation and transitions to consumed when depleted', function () {
    $company = Company::factory()->create();
    $warehouse = AssetWarehouse::factory()->company($company)->create();
    $site = AssetSite::factory()->company($company)->custodyZone()->create();
    $product = AssetProduct::factory()->company($company)->create([
        'type' => AssetProductType::Consumable,
        'cost' => 10,
    ]);

    $emp1 = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);
    $emp2 = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);
    $emp3 = Employee::factory()->create(['company_id' => $company->id, 'user_id' => User::factory(), 'is_active' => true]);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    $service = app(AssetCustodyService::class);

    $service->issue($batch, [
        'employee_ids' => [$emp1->id, $emp2->id, $emp3->id],
        'site_id'      => $site->id,
        'quantity'     => 2,
        'note'         => 'توزيع قرطاسية',
    ]);

    $batch->refresh();
    expect((float) $batch->quantity)->toBe(4.0)
        ->and($batch->status)->toBe(AssetStatus::Available)
        ->and($batch->warehouse_id)->toBe($warehouse->id)
        ->and($batch->movements()->where('type', MovementType::Issued)->count())->toBe(3)
        ->and((float) $batch->movements()->where('type', MovementType::Issued)->sum('quantity'))->toBe(6.0);

    expect(fn () => $service->issue($batch, [
        'employee_ids' => [$emp1->id, $emp2->id],
        'quantity'     => 3,
    ]))->toThrow(ValidationException::class);

    $service->issue($batch, [
        'employee_ids' => [$emp1->id],
        'quantity'     => 4,
    ]);

    $batch->refresh();
    expect((float) $batch->quantity)->toBe(0.0)
        ->and($batch->status)->toBe(AssetStatus::Consumed)
        ->and($batch->warehouse_id)->toBeNull();
});

it('rejects issuing a fixed asset to several employees at once', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $employee = custodyEmployee($company);
    $colleague = custodyEmployee($company);
    $asset = custodyAsset($company, ['warehouse_id' => $warehouse->id]);

    expect(fn () => custodyService()->issue($asset, [
        'employee_ids' => [$employee->id, $colleague->id],
    ]))->toThrow(ValidationException::class, __('assets::services/asset-custody.single-employee-only-for-assets'));

    expect($asset->fresh()->status)->toBe(AssetStatus::Available);

    custodyService()->issue($asset, ['employee_id' => $employee->id]);

    expect($asset->fresh()->current_employee_id)->toBe($employee->id);
});

it('rejects consumable multi-recipient issuance when batch issuance is disabled', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company, ['type' => AssetProductType::Consumable]);
    $employee = custodyEmployee($company);
    $colleague = custodyEmployee($company);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 10,
    ]);

    $settings = app(AssetSettings::class);
    $settings->enable_consumables_batch_issue = false;
    $settings->save();

    expect(fn () => custodyService()->issue($batch, [
        'employee_ids' => [$employee->id, $colleague->id],
    ]))->toThrow(ValidationException::class, __('assets::services/asset-custody.batch-issue-disabled'));

    custodyService()->issue($batch, ['employee_ids' => [$employee->id]]);

    expect((float) $batch->fresh()->quantity)->toBe(9.0);
});

it('keeps consumable site and attachment requirements independent from custody rules', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company, ['type' => AssetProductType::Consumable]);
    $employee = custodyEmployee($company);
    $assetProduct = custodyProduct($company);
    $fixedAsset = custodyAsset($company, [
        'warehouse_id' => $warehouse->id,
        'product_id'   => $assetProduct->id,
    ]);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 100,
    ]);

    $settings = app(AssetSettings::class);
    $settings->require_site_on_custody = true;
    $settings->require_attachments_on_custody = true;
    $settings->require_site_on_consumable_issue = false;
    $settings->require_attachments_on_consumable_issue = false;
    $settings->save();

    expect(fn () => custodyService()->issue($fixedAsset, [
        'employee_id' => $employee->id,
    ]))->toThrow(ValidationException::class, __('assets::services/asset-custody.site-required'));

    $movement = custodyService()->issue($batch, [
        'employee_ids' => [$employee->id],
        'quantity'     => 5,
    ]);

    expect($movement->type)->toBe(MovementType::Issued)
        ->and((float) $batch->fresh()->quantity)->toBe(95.0);
});

it('enforces consumable-only site and attachment requirements', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $site = custodySite($company);
    $product = custodyProduct($company, ['type' => AssetProductType::Consumable]);
    $employee = custodyEmployee($company);
    $assetProduct = custodyProduct($company);
    $fixedAsset = custodyAsset($company, [
        'warehouse_id' => $warehouse->id,
        'product_id'   => $assetProduct->id,
    ]);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 20,
    ]);

    $settings = app(AssetSettings::class);
    $settings->require_site_on_custody = false;
    $settings->require_attachments_on_custody = false;
    $settings->require_site_on_consumable_issue = true;
    $settings->require_attachments_on_consumable_issue = true;
    $settings->save();

    expect(fn () => custodyService()->issue($batch, [
        'employee_ids' => [$employee->id],
    ]))->toThrow(ValidationException::class, __('assets::services/asset-custody.site-required'));

    expect(fn () => custodyService()->issue($batch, [
        'employee_ids' => [$employee->id],
        'site_id'      => $site->id,
    ]))->toThrow(ValidationException::class, __('assets::services/asset-custody.attachments-required'));

    custodyService()->issue($batch, [
        'employee_ids' => [$employee->id],
        'site_id'      => $site->id,
        'quantity'     => 2,
        'attachments'  => ['assets/receipts/handover.pdf'],
    ]);

    expect((float) $batch->fresh()->quantity)->toBe(18.0);

    custodyService()->issue($fixedAsset, ['employee_id' => $employee->id]);

    expect($fixedAsset->fresh()->current_employee_id)->toBe($employee->id);
});

it('uses the configured default consumable issue quantity when none is provided', function () {
    $company = custodyCompany();
    $warehouse = custodyWarehouse($company);
    $product = custodyProduct($company, ['type' => AssetProductType::Consumable]);
    $employee = custodyEmployee($company);
    $colleague = custodyEmployee($company);

    $batch = Asset::query()->create([
        'company_id'       => $company->id,
        'product_id'       => $product->id,
        'warehouse_id'     => $warehouse->id,
        'initial_quantity' => 20,
    ]);

    $settings = app(AssetSettings::class);
    $settings->default_consumable_issue_quantity = 3;
    $settings->save();

    custodyService()->issue($batch, [
        'employee_ids' => [$employee->id, $colleague->id],
    ]);

    expect((float) $batch->fresh()->quantity)->toBe(14.0)
        ->and((float) $batch->movements()->where('type', MovementType::Issued)->sum('quantity'))->toBe(6.0);
});

it('records both Registered and Issued movements when registering an asset directly in custody', function () {
    $company = custodyCompany();
    $product = custodyProduct($company);
    $employee = custodyEmployee($company);
    $custodyZone = custodySite($company);
    $siteLocation = AssetSite::factory()->company($company)->assetLocation()->create();

    $directEmployeeAsset = Asset::query()->create([
        'company_id'          => $company->id,
        'product_id'          => $product->id,
        'status'              => AssetStatus::Held,
        'current_employee_id' => $employee->id,
        'current_site_id'     => $custodyZone->id,
        'custody_note'        => 'Direct handover on registration',
        'initial_attachments' => ['assets/attachments/direct-handover.pdf'],
    ]);

    $movements = AssetMovement::query()
        ->where('asset_id', $directEmployeeAsset->id)
        ->orderBy('id')
        ->get();

    expect($movements)->toHaveCount(2)
        ->and($movements[0]->type)->toBe(MovementType::Registered)
        ->and($movements[0]->source_type)->toBe(AssetSourceType::Manual)
        ->and($movements[0]->to_employee_id)->toBe($employee->id)
        ->and($movements[0]->to_site_id)->toBe($custodyZone->id)
        ->and($movements[1]->type)->toBe(MovementType::Issued)
        ->and($movements[1]->source_type)->toBe(AssetSourceType::Manual)
        ->and($movements[1]->from_display)->toBe(AssetSourceType::Manual->getLabel())
        ->and($movements[1]->to_employee_id)->toBe($employee->id)
        ->and($movements[1]->to_site_id)->toBe($custodyZone->id)
        ->and($movements[1]->note)->toBe('Direct handover on registration')
        ->and($movements[1]->attachments)->toBe(['assets/attachments/direct-handover.pdf']);

    $bulkSiteAssets = custodyService()->bulkRegister([
        'company_id'        => $company->id,
        'product_id'        => $product->id,
        'initial_placement' => 'site_asset',
        'site_id'           => $siteLocation->id,
        'tracking_mode'     => 'with_serials',
        'items'             => [
            ['serial_no' => 'DIRECT-SITE-SN-1', 'asset_tag' => 'AST-DIR-001'],
        ],
    ]);

    $bulkMovements = AssetMovement::query()
        ->where('asset_id', $bulkSiteAssets->first()->id)
        ->orderBy('id')
        ->get();

    expect($bulkMovements)->toHaveCount(2)
        ->and($bulkMovements[0]->type)->toBe(MovementType::Registered)
        ->and($bulkMovements[0]->source_type)->toBe(AssetSourceType::Bulk)
        ->and($bulkMovements[1]->type)->toBe(MovementType::Issued)
        ->and($bulkMovements[1]->source_type)->toBe(AssetSourceType::Bulk)
        ->and($bulkMovements[1]->from_display)->toBe(AssetSourceType::Bulk->getLabel())
        ->and($bulkMovements[1]->to_site_id)->toBe($siteLocation->id)
        ->and($bulkMovements[1]->to_employee_id)->toBeNull();
});
