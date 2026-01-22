<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Product\Filament\Resources\CategoryResource::class  => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Product\Filament\Resources\AttributeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Product\Filament\Resources\PackagingResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
        'exclude' => [
            \Webkul\Product\Filament\Resources\AttributeResource::class,
            \Webkul\Product\Filament\Resources\CategoryResource::class,
            \Webkul\Product\Filament\Resources\PackagingResource::class,
            \Webkul\Product\Filament\Resources\PriceListResource::class,
            \Webkul\Product\Filament\Resources\ProductResource::class,
        ],
    ],

];
