<?php

use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Filament\Resources\PackagingResource;
use Webkul\Product\Filament\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\ProductResource;

return [
    'resources' => [
        'manage' => [
            CategoryResource::class  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            AttributeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            PackagingResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
    ],
];
