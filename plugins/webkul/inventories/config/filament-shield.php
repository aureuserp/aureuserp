<?php

use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            PackagingResource::class => [...$basic, ...$delete, ...$reorder],
            ReceiptResource::class => [...$basic, ...$delete],
            DeliveryResource::class => [...$basic, ...$delete],
            InternalResource::class => [...$basic, ...$delete],
            DropshipResource::class => [...$basic, ...$delete],
            QuantityResource::class => [...$basic, ...$delete],
            ScrapResource::class => [...$basic, ...$delete],
            PackageResource::class => [...$basic, ...$delete],
            LotResource::class => [...$basic, ...$delete],
            WarehouseResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            LocationResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            OperationTypeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            RuleResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            StorageCategoryResource::class => [...$basic, ...$delete, ...$reorder],
            ProductCategoryResource::class => [...$basic, ...$delete],
            ProductAttributeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            PackageTypeResource::class => [...$basic, ...$delete, ...$reorder],
            RouteResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ReplenishmentResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ProductResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
        ],
        'exclude' => [
            OperationResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
            Operations::class,
            Products::class,
        ],
    ],
];
