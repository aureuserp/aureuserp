<?php

use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Filament\Resources\PackagingResource;
use Webkul\Product\Filament\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\ProductResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            CategoryResource::class => $permissions['BASIC'],
            AttributeResource::class => $permissions['FULL'],
            PackagingResource::class => $permissions['REORDER'],
            PriceListResource::class => $permissions['REORDER'],
            ProductResource::class => $permissions['FULL'],
        ],
    ],
];
