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

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            QuotationResource::class => [...$basic, ...$delete],
            PurchaseOrderResource::class => [...$basic, ...$delete],
            PurchaseAgreementResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            VendorResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            VendorPriceResource::class => [...$basic, ...$delete, ...$reorder],
            ProductCategoryResource::class => [...$basic, ...$delete],
            ProductAttributeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            PackagingResource::class => [...$basic, ...$delete, ...$reorder],
            CurrencyResource::class => [...$basic, ...$delete],
            ProductResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            AccountOrderResource::class => [...$basic, ...$delete],
            AccountPurchaseOrderResource::class => [...$basic, ...$delete],
            AccountQuotationResource::class => [...$basic, ...$delete],
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
