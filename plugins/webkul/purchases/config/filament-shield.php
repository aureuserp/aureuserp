<?php

use Webkul\Purchase\Filament\Admin\Clusters\Configurations;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource;
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
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\OrderResource as AccountOrderResource;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\PurchaseOrderResource as AccountPurchaseOrderResource;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\QuotationResource as AccountQuotationResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            QuotationResource::class => $permissions['BASIC'],
            OrderResource::class => $permissions['BASIC'],
            PurchaseOrderResource::class => $permissions['BASIC'],
            PurchaseAgreementResource::class => $permissions['SOFT_DELETE'],
            VendorResource::class => $permissions['SOFT_DELETE'],
            VendorPriceResource::class => $permissions['REORDER'],
            ProductCategoryResource::class => $permissions['BASIC'],
            ProductAttributeResource::class => $permissions['FULL'],
            PackagingResource::class => $permissions['REORDER'],
            CurrencyResource::class => $permissions['BASIC'],
            ProductResource::class => $permissions['FULL'],
            AccountOrderResource::class => $permissions['BASIC'],
            AccountPurchaseOrderResource::class => $permissions['BASIC'],
            AccountQuotationResource::class => $permissions['BASIC'],
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
