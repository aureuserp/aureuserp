<?php

use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccountResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncotermResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroupResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxResource;
use Webkul\Invoice\Filament\Clusters\Customers;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource as InvoicePaymentResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource as InvoiceProductResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            CustomerResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            PaymentResource::class          => [...$basic, ...$delete],
            CreditNoteResource::class       => [...$basic, ...$delete, ...$reorder],
            InvoiceResource::class          => [...$basic, ...$delete, ...$reorder],
            BillResource::class             => [...$basic, ...$delete, ...$reorder],
            VendorResource::class           => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            RefundResource::class           => [...$basic, ...$delete, ...$reorder],
            BankAccountResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            PaymentTermResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ProductCategoryResource::class  => [...$basic, ...$delete],
            ProductAttributeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            TaxGroupResource::class         => [...$basic, ...$delete, ...$reorder],
            TaxResource::class              => [...$basic, ...$delete, ...$reorder],
            CurrencyResource::class         => [...$basic, ...$delete],
            IncotermResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ProductResource::class          => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
        ],
        'exclude' => [
            InvoiceProductResource::class,
            InvoicePaymentResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Vendors::class,
            Customers::class,
            Configuration::class,
        ],
    ],
];
