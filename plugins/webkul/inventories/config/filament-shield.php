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

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            PackagingResource::class => $permissions['REORDER'],
            ReceiptResource::class => $permissions['BASIC'],
            DeliveryResource::class => $permissions['BASIC'],
            InternalResource::class => $permissions['BASIC'],
            DropshipResource::class => $permissions['BASIC'],
            QuantityResource::class => $permissions['BASIC'],
            ScrapResource::class => $permissions['BASIC'],
            PackageResource::class => $permissions['BASIC'],
            LotResource::class => $permissions['BASIC'],
            WarehouseResource::class => $permissions['FULL'],
            LocationResource::class => $permissions['SOFT_DELETE'],
            OperationTypeResource::class => $permissions['FULL'],
            RuleResource::class => $permissions['FULL'],
            StorageCategoryResource::class => $permissions['REORDER'],
            ProductCategoryResource::class => $permissions['BASIC'],
            ProductAttributeResource::class => $permissions['FULL'],
            PackageTypeResource::class => $permissions['REORDER'],
            RouteResource::class => $permissions['FULL'],
            ReplenishmentResource::class => $permissions['SOFT_DELETE'],
            ProductResource::class => $permissions['FULL'],
        ],
        'exclude' => [
            OperationResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
            Operations::class,
            OperationResource::class,
            Products::class,
        ],
    ],

];
