<?php

use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Product\Filament\Resources\PackagingResource;
use Webkul\Product\Filament\Resources\PriceListResource;
use Webkul\Product\Filament\Resources\ProductResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            CategoryResource::class => [...$basic, ...$delete],
            AttributeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            PackagingResource::class => [...$basic, ...$delete, ...$reorder],
            PriceListResource::class => [...$basic, ...$delete, ...$reorder],
            ProductResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
        ],
    ],
];
