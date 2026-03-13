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

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            QuotationResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            OrderResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            OrderToInvoiceResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            OrderToUpsellResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            CustomerResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ProductResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ActivityPlanResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ActivityTypeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            TeamResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ProductCategoryResource::class => [...$basic, ...$delete],
            ProductAttributeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            TagResource::class => [...$basic, ...$delete],
            PackagingResource::class => [...$basic, ...$delete, ...$reorder],
            CurrencyResource::class => [...$basic, ...$delete],
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
