<?php

use Webkul\Manufacturing\Filament\Clusters\Configurations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource;
use Webkul\Manufacturing\Filament\Clusters\Products;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage'  => [
            BillsOfMaterialResource::class    => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            LotResource::class                => [...$basic, ...$delete],
            ManufacturingOrderResource::class => [...$basic, ...$delete],
            OperationResource::class          => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ProductResource::class            => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            WorkOrderResource::class          => [...$basic, ...$delete],
            WorkCenterResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
            Operations::class,
            Products::class,
        ],
    ],
];
