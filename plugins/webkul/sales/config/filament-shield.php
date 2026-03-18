<?php

use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\PackagingResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\Products;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Sale\Filament\Clusters\ToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToUpsellResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            QuotationResource::class => $permissions['SOFT_DELETE'],
            OrderResource::class => $permissions['SOFT_DELETE'],
            OrderToInvoiceResource::class => $permissions['SOFT_DELETE'],
            OrderToUpsellResource::class => $permissions['SOFT_DELETE'],
            CustomerResource::class => $permissions['SOFT_DELETE'],
            ProductResource::class => $permissions['FULL'],
            ActivityPlanResource::class => $permissions['SOFT_DELETE'],
            ActivityTypeResource::class => $permissions['FULL'],
            TeamResource::class => $permissions['FULL'],
            ProductCategoryResource::class => $permissions['BASIC'],
            ProductAttributeResource::class => $permissions['FULL'],
            TagResource::class => $permissions['BASIC'],
            PackagingResource::class => $permissions['REORDER'],
            CurrencyResource::class => $permissions['BASIC'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configuration::class,
            Orders::class,
            Products::class,
            ToInvoice::class,
        ],
    ],

];
