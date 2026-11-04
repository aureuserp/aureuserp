<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

use Spatie\Permission\Models\Role;
use Webkul\Asset\Database\Seeders\RolePermissionSeeder;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Policies\AssetPolicy;
use Webkul\Asset\Policies\AssetProductPolicy;
use Webkul\Asset\Policies\AssetSitePolicy;
use Webkul\Asset\Policies\AssetWarehousePolicy;
use Webkul\Asset\Policies\Concerns\ChecksAssetScopedAccess;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('authorizes company asset abilities through shield permission identifiers', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'create_asset_asset',
        'update_asset_asset',
        'delete_asset_asset',
        'restore_asset_asset',
        'force_delete_asset_asset',
    ]);

    $asset = Asset::factory()->create();
    $policy = new AssetPolicy;

    expect($policy->viewAny($user))->toBeTrue()
        ->and($policy->view($user, $asset))->toBeTrue()
        ->and($policy->create($user))->toBeTrue()
        ->and($policy->update($user, $asset))->toBeTrue()
        ->and($policy->delete($user, $asset))->toBeTrue()
        ->and($policy->restore($user, $asset))->toBeTrue()
        ->and($policy->forceDelete($user, $asset))->toBeTrue()
        ->and($user->can('viewAny', Asset::class))->toBeTrue();
});

it('denies company asset abilities without shield permission identifiers', function () {
    $user = FilamentHelper::actingAs([]);
    $asset = Asset::factory()->create();
    $policy = new AssetPolicy;

    expect($policy->viewAny($user))->toBeFalse()
        ->and($policy->view($user, $asset))->toBeFalse()
        ->and($policy->create($user))->toBeFalse()
        ->and($policy->update($user, $asset))->toBeFalse()
        ->and($policy->delete($user, $asset))->toBeFalse()
        ->and($user->can('viewAny', Asset::class))->toBeFalse();
});

it('authorizes company asset site abilities through distinct shield permission identifiers', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'view_asset_asset::site',
        'create_asset_asset::site',
        'update_asset_asset::site',
        'delete_asset_asset::site',
        'restore_asset_asset::site',
        'force_delete_asset_asset::site',
    ]);

    $site = AssetSite::factory()->create();
    $policy = new AssetSitePolicy;

    expect($policy->viewAny($user))->toBeTrue()
        ->and($policy->view($user, $site))->toBeTrue()
        ->and($policy->create($user))->toBeTrue()
        ->and($policy->update($user, $site))->toBeTrue()
        ->and($policy->delete($user, $site))->toBeTrue()
        ->and($policy->restore($user, $site))->toBeTrue()
        ->and($policy->forceDelete($user, $site))->toBeTrue()
        ->and($user->can('viewAny', AssetSite::class))->toBeTrue();
});

it('keeps asset and site permissions independent', function () {
    $user = FilamentHelper::actingAs(['view_any_asset_asset']);

    $assetPolicy = new AssetPolicy;
    $sitePolicy = new AssetSitePolicy;

    expect($assetPolicy->viewAny($user))->toBeTrue()
        ->and($sitePolicy->viewAny($user))->toBeFalse();
});

it('authorizes company asset warehouse abilities through distinct shield permission identifiers', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset::warehouse',
        'view_asset_asset::warehouse',
        'create_asset_asset::warehouse',
        'update_asset_asset::warehouse',
        'delete_asset_asset::warehouse',
        'restore_asset_asset::warehouse',
        'force_delete_asset_asset::warehouse',
    ]);

    $warehouse = AssetWarehouse::factory()->create();
    $policy = new AssetWarehousePolicy;

    expect($policy->viewAny($user))->toBeTrue()
        ->and($policy->view($user, $warehouse))->toBeTrue()
        ->and($policy->create($user))->toBeTrue()
        ->and($policy->update($user, $warehouse))->toBeTrue()
        ->and($policy->delete($user, $warehouse))->toBeTrue()
        ->and($policy->restore($user, $warehouse))->toBeTrue()
        ->and($policy->forceDelete($user, $warehouse))->toBeTrue()
        ->and($user->can('viewAny', AssetWarehouse::class))->toBeTrue();
});

it('authorizes asset product abilities through shield permission identifiers', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_product',
        'create_asset_product',
    ]);

    $policy = new AssetProductPolicy;
    $product = AssetProduct::factory()->create();

    expect($policy->viewAny($user))->toBeTrue()
        ->and($policy->create($user))->toBeTrue();
});

it('authorizes asset product abilities through asset fallback permission identifiers', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'create_asset_asset',
    ]);

    $policy = new AssetProductPolicy;
    $product = AssetProduct::factory()->create();

    expect($policy->viewAny($user))->toBeTrue()
        ->and($policy->create($user))->toBeFalse();
});

it('restricts custody actions when user has view-only abilities', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
    ]);

    $asset = Asset::factory()->create();

    expect(AssetResource::canCreate())->toBeFalse()
        ->and(AssetResource::canEdit($asset))->toBeFalse()
        ->and($user->can('terminate', $asset))->toBeFalse();
});

it('authorizes custody officer with update_asset_asset for issue/transfer/return but blocks create and terminate', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'update_asset_asset',
    ]);

    $asset = Asset::factory()->create();
    $product = AssetProduct::factory()->create();

    expect(AssetResource::canEdit($asset))->toBeTrue()
        ->and(AssetResource::canCreate())->toBeFalse()
        ->and($user->can('terminate', $asset))->toBeFalse()
        ->and($user->can('update', $product))->toBeFalse()
        ->and($user->can('delete', $product))->toBeFalse();
});

it('authorizes asset manager with delete_asset_asset for terminate action', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'delete_asset_asset',
    ]);

    $asset = Asset::factory()->create();

    expect($user->can('terminate', $asset))->toBeTrue();
});

it('enforces restrict_termination_to_managers and threshold in AssetPolicy::terminate', function () {
    $settings = app(AssetSettings::class);
    $settings->restrict_termination_to_managers = true;
    $settings->termination_approval_cost_threshold = 1000;
    $settings->save();

    // Individual permission user
    $officerUser = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'delete_asset_asset',
    ]);
    $officerUser->update(['resource_permission' => PermissionType::INDIVIDUAL->value]);

    $highCostAsset = Asset::factory()->create(['cost' => 1500, 'creator_id' => $officerUser->id]);
    $lowCostAsset = Asset::factory()->create(['cost' => 500, 'creator_id' => $officerUser->id]);

    expect($officerUser->can('terminate', $highCostAsset))->toBeFalse()
        ->and($officerUser->can('terminate', $lowCostAsset))->toBeTrue();

    // Global permission (manager) user
    $managerUser = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'delete_asset_asset',
    ]);
    $managerUser->update(['resource_permission' => PermissionType::GLOBAL->value]);

    expect($managerUser->can('terminate', $highCostAsset))->toBeTrue()
        ->and($managerUser->can('terminate', $lowCostAsset))->toBeTrue();
});

it('enforces individual scope access on asset records in AssetPolicy', function () {
    $user = FilamentHelper::actingAs([
        'view_any_asset_asset',
        'view_asset_asset',
        'update_asset_asset',
    ]);
    $user->update(['resource_permission' => PermissionType::INDIVIDUAL->value]);

    $ownAsset = Asset::factory()->create(['creator_id' => $user->id]);
    $otherAsset = Asset::factory()->create(['creator_id' => User::factory()->create()->id]);

    expect($user->can('view', $ownAsset))->toBeTrue()
        ->and($user->can('view', $otherAsset))->toBeFalse()
        ->and($user->can('update', $ownAsset))->toBeTrue()
        ->and($user->can('update', $otherAsset))->toBeFalse()
        ->and($user->can('issue', $ownAsset))->toBeTrue()
        ->and($user->can('transfer', $ownAsset))->toBeTrue()
        ->and($user->can('return', $ownAsset))->toBeTrue();
});

it('seeds all asset permissions including product and movement permissions for super_admin and standard roles', function () {
    $superAdmin = Role::query()->firstOrCreate([
        'name'       => 'super_admin',
        'guard_name' => 'web',
    ]);
    $defaultAdmin = Role::query()->firstOrCreate([
        'name'       => 'Admin',
        'guard_name' => 'web',
    ], [
        'is_default' => true,
    ]);

    app(RolePermissionSeeder::class)->run();

    $superAdmin->refresh();
    $defaultAdmin->refresh();
    $assetManager = Role::query()->where('name', 'Asset Manager')->firstOrFail();
    $assetOfficer = Role::query()->where('name', 'Asset Officer')->firstOrFail();
    $custodyManager = Role::query()->where('name', 'Custody Manager')->firstOrFail();
    $custodyOfficer = Role::query()->where('name', 'Custody Officer')->firstOrFail();

    expect($superAdmin->hasPermissionTo('create_asset_product'))->toBeTrue()
        ->and($defaultAdmin->hasPermissionTo('view_any_asset_asset'))->toBeTrue()
        ->and($defaultAdmin->hasPermissionTo('create_asset_product'))->toBeTrue()
        ->and($superAdmin->hasPermissionTo('update_asset_product'))->toBeTrue()
        ->and($superAdmin->hasPermissionTo('delete_asset_product'))->toBeTrue()
        ->and($superAdmin->hasPermissionTo('delete_any_asset_product'))->toBeTrue()
        ->and($superAdmin->hasPermissionTo('view_any_asset_asset::movement'))->toBeTrue()
        ->and($assetManager->hasPermissionTo('create_asset_product'))->toBeTrue()
        ->and($assetManager->hasPermissionTo('update_asset_product'))->toBeTrue()
        ->and($assetManager->hasPermissionTo('delete_asset_product'))->toBeTrue()
        ->and($assetManager->hasPermissionTo('delete_any_asset_product'))->toBeTrue()
        ->and($assetManager->hasPermissionTo('page_asset_manage_assets'))->toBeTrue()
        ->and($assetOfficer->hasPermissionTo('create_asset_product'))->toBeTrue()
        ->and($assetOfficer->hasPermissionTo('update_asset_product'))->toBeTrue()
        ->and($assetOfficer->hasPermissionTo('delete_asset_product'))->toBeFalse()
        ->and($custodyManager->hasPermissionTo('view_any_asset_product'))->toBeTrue()
        ->and($custodyManager->hasPermissionTo('create_asset_product'))->toBeFalse()
        ->and($custodyOfficer->hasPermissionTo('view_any_asset_product'))->toBeTrue()
        ->and($custodyOfficer->hasPermissionTo('update_asset_product'))->toBeFalse();
});

it('fails closed in ChecksAssetScopedAccess when candidate user ids are empty for scoped user', function () {
    $user = FilamentHelper::actingAs([]);
    $user->update(['resource_permission' => PermissionType::INDIVIDUAL->value]);

    $checker = new class
    {
        use ChecksAssetScopedAccess;

        public function check(User $user, array $candidates): bool
        {
            return $this->hasScopedUserAccess($user, $candidates);
        }
    };

    expect($checker->check($user, [null, null]))->toBeFalse()
        ->and($checker->check($user, []))->toBeFalse()
        ->and($checker->check($user, [$user->id]))->toBeTrue();
});
