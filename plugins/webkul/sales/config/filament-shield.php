<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Sale\Filament\Clusters\Configuration\Resources\PackagingResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Sale\Filament\Clusters\Configuration::class,
            \Webkul\Sale\Filament\Clusters\Orders::class,
            \Webkul\Sale\Filament\Clusters\Products::class,
            \Webkul\Sale\Filament\Clusters\ToInvoice::class,
        ],
    ],

];
