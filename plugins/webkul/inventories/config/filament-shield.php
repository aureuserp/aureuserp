<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\DropshipResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource::class => ['view_any', 'create'],
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
        'exclude' => [
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Inventory\Filament\Clusters\Configurations::class,
            \Webkul\Inventory\Filament\Clusters\Operations::class,
            \Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource::class,
            \Webkul\Inventory\Filament\Clusters\Products::class,
        ],
    ],

];
