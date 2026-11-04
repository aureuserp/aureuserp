<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

use Livewire\Livewire;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageAssets as GlobalManageAssets;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageCustody as GlobalManageCustody;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageGovernance as GlobalManageGovernance;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageWarehouses as GlobalManageWarehouses;
use Webkul\Asset\Filament\Pages\ManageAssets;
use Webkul\Asset\Filament\Pages\ManageCustody;
use Webkul\Asset\Filament\Pages\ManageGovernance;
use Webkul\Asset\Filament\Pages\ManageWarehouses;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\Sequence;

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
    $settings->save();
});

it('forbids asset settings page without permission (Test 18)', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ManageAssets::class)->assertForbidden();
});

it('allows asset settings page with permission and renders form (Test 18)', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    Livewire::test(ManageAssets::class)
        ->assertSuccessful()
        ->assertSee(__('assets::filament/pages/manage-assets.title'));
});

it('saves settings and synchronizes existing sequence rows (Test 16)', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    $company = Company::query()->firstOrFail();
    $custodyService = app(AssetCustodyService::class);

    $firstTag = $custodyService->nextAssetTag($company->id);
    expect($firstTag)->toBe('AST-00001');

    $sequence = Sequence::withoutGlobalScope(CompanyScope::class)
        ->where('code', AssetCustodyService::SEQUENCE_CODE)
        ->where('company_id', $company->id)
        ->firstOrFail();
    expect($sequence->prefix)->toBe('AST-');

    Livewire::test(ManageAssets::class)
        ->fillForm([
            'tag_prefix'                => 'IT-',
            'auto_generate_tag'         => true,
            'default_condition'         => AssetCondition::Good->value,
            'require_serial_by_default' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(AssetSettings::class);
    expect($settings->tag_prefix)->toBe('IT-')
        ->and($settings->default_condition)->toBe('good')
        ->and($settings->require_serial_by_default)->toBeTrue();

    $sequence->refresh();
    expect($sequence->prefix)->toBe('IT-');

    $nextTag = $custodyService->nextAssetTag($company->id);
    expect($nextTag)->toBe('IT-00002');
});

it('saves default warehouse and fixed site settings', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    $company = Company::query()->firstOrFail();
    $warehouse = AssetWarehouse::factory()->company($company)->create(['name' => 'HQ IT Depot']);

    Livewire::test(ManageWarehouses::class)
        ->fillForm([
            'default_warehouse_id' => $warehouse->id,
            'enable_site_assets'   => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(AssetSettings::class);
    expect($settings->default_warehouse_id)->toBe($warehouse->id)
        ->and($settings->enable_site_assets)->toBeTrue();
});

it('saves custody duration and assignment settings', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    Livewire::test(ManageCustody::class)
        ->fillForm([
            'require_site_on_custody'           => true,
            'default_custody_duration_days'     => 45,
            'require_expected_return_date'      => true,
            'prevent_duplicate_product_custody' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(AssetSettings::class);
    expect($settings->require_site_on_custody)->toBeTrue()
        ->and($settings->default_custody_duration_days)->toBe(45)
        ->and($settings->require_expected_return_date)->toBeTrue()
        ->and($settings->prevent_duplicate_product_custody)->toBeTrue();
});

it('saves dedicated consumables issuance settings from the custody page', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    Livewire::test(ManageCustody::class)
        ->fillForm([
            'enable_consumables_batch_issue'          => false,
            'auto_fill_consumable_employees_by_site'  => false,
            'default_consumable_issue_quantity'       => 5,
            'require_site_on_consumable_issue'        => true,
            'require_attachments_on_consumable_issue' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(AssetSettings::class);

    expect($settings->enable_consumables_batch_issue)->toBeFalse()
        ->and($settings->auto_fill_consumable_employees_by_site)->toBeFalse()
        ->and((int) $settings->default_consumable_issue_quantity)->toBe(5)
        ->and($settings->require_site_on_consumable_issue)->toBeTrue()
        ->and($settings->require_attachments_on_consumable_issue)->toBeTrue();
});

it('saves custody attachment and termination governance settings', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    Livewire::test(ManageGovernance::class)
        ->assertFormFieldDoesNotExist('termination_approval_cost_threshold')
        ->fillForm([
            'require_attachments_on_custody'     => true,
            'require_attachments_on_damage'      => true,
            'require_attachments_on_termination' => true,
            'restrict_termination_to_managers'   => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(AssetSettings::class);
    expect($settings->require_attachments_on_custody)->toBeTrue()
        ->and($settings->require_attachments_on_damage)->toBeTrue()
        ->and($settings->require_attachments_on_termination)->toBeTrue()
        ->and($settings->restrict_termination_to_managers)->toBeTrue();
});

it('locks tag_prefix once an asset is registered in the system (Issue 06)', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    $company = Company::query()->firstOrFail();

    Livewire::test(ManageAssets::class)
        ->assertFormFieldIsEnabled('tag_prefix');

    Asset::factory()->company($company)->create();

    Livewire::test(ManageAssets::class)
        ->assertFormFieldIsDisabled('tag_prefix')
        ->fillForm([
            'default_condition' => AssetCondition::Fair->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AssetSettings::class)->tag_prefix)->toBe('AST-');
});

it('saves all extended settings including padding, duration, and thresholds', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    $company = Company::query()->firstOrFail();

    Livewire::test(GlobalManageAssets::class)
        ->fillForm([
            'auto_generate_tag'         => true,
            'tag_prefix'                => 'EQP-',
            'tag_padding'               => 4,
            'default_condition'         => AssetCondition::New->value,
            'require_serial_by_default' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    Livewire::test(GlobalManageWarehouses::class)
        ->fillForm([
            'enable_site_assets' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    Livewire::test(GlobalManageCustody::class)
        ->fillForm([
            'default_custody_duration_days'     => 45,
            'require_expected_return_date'      => true,
            'prevent_duplicate_product_custody' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    Livewire::test(GlobalManageGovernance::class)
        ->assertFormFieldDoesNotExist('termination_approval_cost_threshold')
        ->fillForm([
            'require_attachments_on_termination' => true,
            'restrict_termination_to_managers'   => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(AssetSettings::class);
    expect($settings->tag_prefix)->toBe('EQP-')
        ->and($settings->tag_padding)->toBe(4)
        ->and($settings->default_custody_duration_days)->toBe(45)
        ->and($settings->require_expected_return_date)->toBeTrue()
        ->and($settings->prevent_duplicate_product_custody)->toBeTrue()
        ->and($settings->require_attachments_on_termination)->toBeTrue()
        ->and($settings->restrict_termination_to_managers)->toBeTrue();

    $sequence = Sequence::withoutGlobalScope(CompanyScope::class)
        ->where('code', AssetCustodyService::SEQUENCE_CODE)
        ->first();

    if ($sequence) {
        expect($sequence->prefix)->toBe('EQP-')
            ->and($sequence->padding)->toBe(4);
    }
});

it('prevents disabling enable_site_assets in beforeSave when active site assets exist', function () {
    FilamentHelper::actingAs(['page_asset_manage_assets']);

    $company = Company::query()->firstOrFail();
    $site = AssetSite::factory()->company($company)->create(['usage_type' => AssetSiteUsage::AssetLocation]);

    Asset::factory()->company($company)->heldBySite($site)->create();

    app(AssetSettings::class)->fill(['enable_site_assets' => true])->save();

    Livewire::test(ManageWarehouses::class)
        ->fillForm([
            'enable_site_assets' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AssetSettings::class)->enable_site_assets)->toBeTrue();
});
