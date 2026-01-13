<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\PackagingResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
        'exclude' => [
            \Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Purchase\Filament\Admin\Clusters\Orders::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Configurations::class,
            \Webkul\Purchase\Filament\Admin\Clusters\Products::class,
        ],
    ],

];
