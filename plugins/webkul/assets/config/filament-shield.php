<?php

use Webkul\Asset\Filament\Clusters\Configurations;
use Webkul\Asset\Filament\Clusters\PluginSettings;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageAssets;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageCustody;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageGovernance;
use Webkul\Asset\Filament\Clusters\Settings\Pages\ManageWarehouses;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;
use Webkul\Asset\Filament\Resources\AssetMovementResource;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Filament\Resources\ProductResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];

return [
    'resources' => [
        'manage' => [
            AssetResource::class          => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ProductResource::class        => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            AssetCategoryResource::class  => [...$basic, ...$delete],
            AssetSiteResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            AssetWarehouseResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            AssetMovementResource::class  => ['view_any', 'view'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
            PluginSettings::class,
            ManageAssets::class,
            ManageCustody::class,
            ManageGovernance::class,
            ManageWarehouses::class,
        ],
    ],
];
