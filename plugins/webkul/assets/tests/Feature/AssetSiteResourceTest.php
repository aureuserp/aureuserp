<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/CompanyHelper.php';

use Filament\Actions\DeleteAction;
use Livewire\Livewire;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\CreateAssetSite;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\EditAssetSite;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\ListAssetSites;
use Webkul\Asset\Filament\Resources\AssetSiteResource\Pages\ViewAssetSite;
use Webkul\Asset\Filament\Resources\AssetSiteResource\RelationManagers\SiteAssetsRelationManager;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

function createSiteEmployee(Company $company, array $attributes = []): Employee
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

it('forbids listing asset sites without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListAssetSites::class)->assertForbidden();
});

it('lists, searches, and filters asset sites', function () {
    FilamentHelper::actingAs(['view_any_asset_asset::site']);

    $officeType = AssetSiteType::whereNull('company_id')->where('name', 'Office')->firstOrFail();
    $serverType = AssetSiteType::whereNull('company_id')->where('name', 'Server Room')->firstOrFail();

    $office = AssetSite::factory()->active()->create([
        'name'         => 'Head Office',
        'site_type_id' => $officeType->id,
    ]);

    $serverRoom = AssetSite::factory()->inactive()->create([
        'name'         => 'Datacenter Room',
        'site_type_id' => $serverType->id,
    ]);

    Livewire::test(ListAssetSites::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$office, $serverRoom])
        ->assertCanRenderTableColumn('assets_count')
        ->searchTable('Head Office')
        ->assertCanSeeTableRecords([$office])
        ->assertCanNotSeeTableRecords([$serverRoom])
        ->searchTable(null)
        ->filterTable('is_active', true)
        ->assertCanSeeTableRecords([$office])
        ->assertCanNotSeeTableRecords([$serverRoom]);
});

it('creates an asset site with a dynamic site type', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'create_asset_asset::site',
    ]);

    $company = Company::query()->firstOrFail();
    $officeType = AssetSiteType::whereNull('company_id')->where('name', 'Office')->firstOrFail();

    Livewire::test(CreateAssetSite::class)
        ->fillForm([
            'name'         => 'Riyadh Branch',
            'code'         => 'SITE-RYD-01',
            'site_type_id' => $officeType->id,
            'company_id'   => $company->id,
            'is_active'    => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $site = AssetSite::where('code', 'SITE-RYD-01')->firstOrFail();

    expect($site->site_type_id)->toBe($officeType->id)
        ->and($site->company_id)->toBe($company->id);
});

it('scopes asset sites to the active company', function () {
    $owningCompany = CompanyHelper::company();
    $otherCompany = CompanyHelper::company();

    $site = AssetSite::factory()->create([
        'company_id' => $owningCompany->id,
        'name'       => 'Other Company Site',
    ]);

    FilamentHelper::actingAsCompanyUser($otherCompany, ['view_any_asset_asset::site']);

    Livewire::test(ListAssetSites::class)
        ->assertOk()
        ->assertCanNotSeeTableRecords([$site]);
});

it('shows currently deployed assets in SiteAssetsRelationManager', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'view_asset_asset::site',
        'view_any_asset_asset',
        'view_asset_asset',
        'update_asset_asset',
    ]);

    $site = AssetSite::factory()->custodyZone()->create();
    $employee = createSiteEmployee($site->company);

    $deployedAsset = Asset::factory()->company($site->company)->heldByEmployee($employee, $site)->create();

    Livewire::test(SiteAssetsRelationManager::class, [
        'ownerRecord' => $site,
        'pageClass'   => ViewAssetSite::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$deployedAsset]);
});

it('blocks deleting a site referenced by assets through the table action', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'delete_asset_asset::site',
    ]);

    $site = AssetSite::factory()->custodyZone()->create();
    $employee = createSiteEmployee($site->company);
    Asset::factory()->company($site->company)->heldByEmployee($employee, $site)->create();

    Livewire::test(ListAssetSites::class)
        ->callTableAction(DeleteAction::class, $site)
        ->assertNotified();

    expect(AssetSite::withTrashed()->find($site->id))->not->toBeNull()
        ->and(AssetSite::withTrashed()->find($site->id)->trashed())->toBeFalse();
});

it('deletes an unreferenced site through the table action', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'delete_asset_asset::site',
    ]);

    $site = AssetSite::factory()->create();

    Livewire::test(ListAssetSites::class)
        ->callTableAction(DeleteAction::class, $site);

    expect(AssetSite::withTrashed()->find($site->id)->trashed())->toBeTrue();
});

it('renders the site edit page', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'view_asset_asset::site',
        'update_asset_asset::site',
    ]);

    $site = AssetSite::factory()->create();

    Livewire::test(EditAssetSite::class, ['record' => $site->id])
        ->assertOk()
        ->assertFormSet([
            'name' => $site->name,
        ]);
});

it('can assign a default custodian to an asset site', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'create_asset_asset::site',
        'update_asset_asset::site',
    ]);

    $company = Company::query()->firstOrFail();
    $custodian = createSiteEmployee($company);
    $officeType = AssetSiteType::whereNull('company_id')->where('name', 'Office')->firstOrFail();

    Livewire::test(CreateAssetSite::class)
        ->fillForm([
            'name'                          => 'Operations Site',
            'code'                          => 'SITE-OPS-01',
            'site_type_id'                  => $officeType->id,
            'company_id'                    => $company->id,
            'default_custodian_employee_id' => $custodian->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $site = AssetSite::query()->where('code', 'SITE-OPS-01')->firstOrFail();
    expect($site->default_custodian_employee_id)->toBe($custodian->id);
});

it('shows assets deployed across the entire subtree in SiteAssetsRelationManager', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'view_asset_asset::site',
        'view_any_asset_asset',
        'view_asset_asset',
        'update_asset_asset',
    ]);

    $parentSite = AssetSite::factory()->custodyZone()->create();
    $childSite = AssetSite::factory()->withParent($parentSite)->create();
    $grandchildSite = AssetSite::factory()->withParent($childSite)->create();
    $employee = createSiteEmployee($parentSite->company);

    $assetRoot = Asset::factory()->company($parentSite->company)->heldByEmployee($employee, $parentSite)->create();
    $assetChild = Asset::factory()->company($parentSite->company)->heldByEmployee($employee, $childSite)->create();
    $assetGrandchild = Asset::factory()->company($parentSite->company)->heldByEmployee($employee, $grandchildSite)->create();

    $otherSite = AssetSite::factory()->company($parentSite->company)->custodyZone()->create();
    $assetOther = Asset::factory()->company($parentSite->company)->heldByEmployee($employee, $otherSite)->create();

    Livewire::test(SiteAssetsRelationManager::class, [
        'ownerRecord' => $parentSite,
        'pageClass'   => ViewAssetSite::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$assetRoot, $assetChild, $assetGrandchild])
        ->assertCanNotSeeTableRecords([$assetOther]);
});

it('filters asset sites by usage_type and parent_id subtree filter', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
    ]);

    $company = Company::query()->firstOrFail();
    $root = AssetSite::factory()->company($company)->assetLocation()->create(['name' => 'HQ Complex']);
    $child = AssetSite::factory()->company($company)->withParent($root)->create(['name' => 'HQ Wing A']);
    $other = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'Remote Yard']);

    Livewire::test(ListAssetSites::class)
        ->assertOk()
        ->filterTable('usage_type', AssetSiteUsage::CustodyZone->value)
        ->assertCanSeeTableRecords([$other])
        ->assertCanNotSeeTableRecords([$root, $child]);

    Livewire::test(ListAssetSites::class)
        ->assertOk()
        ->filterTable('parent_id', $root->id)
        ->assertCanSeeTableRecords([$child])
        ->assertCanNotSeeTableRecords([$other]);
});

it('renders only the all preset table view in SiteAssetsRelationManager when site has no child sites', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'view_asset_asset::site',
        'view_any_asset_asset',
        'view_asset_asset',
    ]);

    $leafSite = AssetSite::factory()->custodyZone()->create();

    $test = Livewire::test(SiteAssetsRelationManager::class, [
        'ownerRecord' => $leafSite,
        'pageClass'   => ViewAssetSite::class,
    ]);

    $test->assertOk();
    expect(array_keys($test->instance()->getPresetTableViews()))->toBe(['all']);
});

it('renders preset table views and filters correctly across a 3-level site tree in SiteAssetsRelationManager', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'view_asset_asset::site',
        'view_any_asset_asset',
        'view_asset_asset',
    ]);

    $company = Company::query()->firstOrFail();
    $rootSite = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'Aden Regional Office']);
    $aptSite = AssetSite::factory()->company($company)->withParent($rootSite)->create(['name' => 'Suite 101']);
    $officeSite = AssetSite::factory()->company($company)->withParent($aptSite)->create(['name' => 'Office Room 1']);
    $otherSite = AssetSite::factory()->company($company)->custodyZone()->create(['name' => 'Mukalla Branch']);

    $employee = createSiteEmployee($company);

    $assetRoot = Asset::factory()->company($company)->heldByEmployee($employee, $rootSite)->create();
    $assetApt = Asset::factory()->company($company)->heldByEmployee($employee, $aptSite)->create();
    $assetOffice = Asset::factory()->company($company)->heldByEmployee($employee, $officeSite)->create();
    $assetOther = Asset::factory()->company($company)->heldByEmployee($employee, $otherSite)->create();

    $test = Livewire::test(SiteAssetsRelationManager::class, [
        'ownerRecord' => $rootSite,
        'pageClass'   => ViewAssetSite::class,
    ]);

    $test->assertOk();

    $tabs = $test->instance()->getPresetTableViews();
    expect($tabs)->toHaveKeys([
        'all',
        'direct',
        'subsites',
    ]);

    expect((int) $tabs['all']->getBadge())->toBe(3)
        ->and((int) $tabs['direct']->getBadge())->toBe(1)
        ->and((int) $tabs['subsites']->getBadge())->toBe(2);

    $test->call('loadView', 'all')
        ->assertCanSeeTableRecords([$assetRoot, $assetApt, $assetOffice])
        ->assertCanNotSeeTableRecords([$assetOther]);

    $test->call('loadView', 'direct')
        ->assertCanSeeTableRecords([$assetRoot])
        ->assertCanNotSeeTableRecords([$assetApt, $assetOffice, $assetOther]);

    $test->call('loadView', 'subsites')
        ->assertCanSeeTableRecords([$assetApt, $assetOffice])
        ->assertCanNotSeeTableRecords([$assetRoot, $assetOther]);

    $test->call('loadView', 'all')
        ->filterTable('current_site_id', $aptSite->id)
        ->assertCanSeeTableRecords([$assetApt, $assetOffice])
        ->assertCanNotSeeTableRecords([$assetRoot, $assetOther]);
});

it('shows archived asset sites under archived preset view and hides them from active views', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'view_asset_asset::site',
        'delete_asset_asset::site',
        'restore_asset_asset::site',
    ]);

    $company = Company::query()->firstOrFail();
    $activeSite = AssetSite::factory()->company($company)->active()->create(['name' => 'Active Branch']);
    $archivedSite = AssetSite::factory()->company($company)->active()->create(['name' => 'Closed Branch']);

    $archivedSite->delete();
    expect($archivedSite->fresh()->trashed())->toBeTrue();

    Livewire::test(ListAssetSites::class)
        ->assertCanSeeTableRecords([$activeSite])
        ->assertCanNotSeeTableRecords([$archivedSite]);

    Livewire::test(ListAssetSites::class)
        ->call('loadView', 'archived')
        ->assertCanSeeTableRecords([$archivedSite])
        ->assertCanNotSeeTableRecords([$activeSite]);
});

it('hides editOptionAction on site_type_id when empty and shows it when selected', function () {
    FilamentHelper::actingAs([
        'view_any_asset_asset::site',
        'create_asset_asset::site',
        'update_asset_asset::site',
    ]);

    $siteType = AssetSiteType::factory()->create();

    $livewire = Livewire::test(CreateAssetSite::class);
    $component = $livewire->instance()->getSchema('form')->getFlatComponents()['site_type_id'];
    $action = $component->getAction('editOption');

    expect($action->isVisible())->toBeFalse();

    $livewire->fillForm(['site_type_id' => $siteType->id]);
    $component = $livewire->instance()->getSchema('form')->getFlatComponents()['site_type_id'];
    $action = $component->getAction('editOption');

    expect($action->isVisible())->toBeTrue();
});
