<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

use Filament\Facades\Filament;
use Webkul\Asset\Filament\Pages\ManageAssets;
use Webkul\Asset\Filament\Pages\ManageCustody;
use Webkul\Asset\Filament\Pages\ManageGovernance;
use Webkul\Asset\Filament\Pages\ManageWarehouses;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;
use Webkul\Asset\Filament\Resources\AssetMovementResource;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Filament\Resources\ProductResource;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('assets');
});

it('registers the company assets plugin on the admin panel', function () {
    FilamentHelper::bootAdminPanel();

    expect(Filament::getPanel('admin')->hasPlugin('assets'))->toBeTrue();
});

it('registers Configurations and PluginSettings clusters with all expected pages and resources', function () {
    FilamentHelper::bootAdminPanel();
    $panel = Filament::getPanel('admin');

    $resources = $panel->getResources();
    expect($resources)->toContain(AssetWarehouseResource::class)
        ->and($resources)->toContain(AssetSiteResource::class)
        ->and($resources)->toContain(AssetCategoryResource::class)
        ->and($resources)->toContain(ProductResource::class)
        ->and($resources)->toContain(AssetMovementResource::class);

    $pages = $panel->getPages();
    expect($pages)->toContain(ManageAssets::class)
        ->and($pages)->toContain(ManageWarehouses::class)
        ->and($pages)->toContain(ManageCustody::class)
        ->and($pages)->toContain(ManageGovernance::class)
        ->and($pages)->toContain(Webkul\Asset\Filament\Clusters\Settings\Pages\ManageAssets::class)
        ->and($pages)->toContain(Webkul\Asset\Filament\Clusters\Settings\Pages\ManageWarehouses::class)
        ->and($pages)->toContain(Webkul\Asset\Filament\Clusters\Settings\Pages\ManageCustody::class)
        ->and($pages)->toContain(Webkul\Asset\Filament\Clusters\Settings\Pages\ManageGovernance::class);
});
