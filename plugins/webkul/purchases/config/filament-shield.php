<?php

use Webkul\Purchase\Filament\Admin\Clusters\Configurations;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\PackagingResource;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductAttributeResource;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPriceResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;
use Webkul\Purchase\Filament\Admin\Clusters\Products;

return [
    'resources' => [
        'manage' => [
            QuotationResource::class                => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            OrderResource::class                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            PurchaseOrderResource::class            => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            PurchaseAgreementResource::class        => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            VendorResource::class                   => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            VendorPriceResource::class              => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            ProductCategoryResource::class          => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            ProductAttributeResource::class         => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            PackagingResource::class                => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
        'exclude' => [
            OrderResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Orders::class,
            Configurations::class,
            Products::class,
        ],
    ],

];
