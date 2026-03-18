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
use Webkul\Invoice\Filament\Clusters\Customer;
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

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            CustomerResource::class => $permissions['SOFT_DELETE'],
            PaymentResource::class => $permissions['BASIC'],
            CreditNoteResource::class => $permissions['REORDER'],
            InvoiceResource::class => $permissions['REORDER'],
            InvoicePaymentResource::class => $permissions['BASIC'],
            BillResource::class => $permissions['REORDER'],
            VendorResource::class => $permissions['SOFT_DELETE'],
            RefundResource::class => $permissions['REORDER'],
            BankAccountResource::class => $permissions['SOFT_DELETE'],
            PaymentTermResource::class => $permissions['FULL'],
            ProductCategoryResource::class => $permissions['BASIC'],
            ProductAttributeResource::class => $permissions['FULL'],
            TaxGroupResource::class => $permissions['REORDER'],
            TaxResource::class => $permissions['REORDER'],
            CurrencyResource::class => $permissions['BASIC'],
            IncotermResource::class => $permissions['SOFT_DELETE'],
            ProductResource::class => $permissions['FULL'],
        ],
        'exclude' => [
            InvoiceProductResource::class,
            InvoicePaymentResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Vendors::class,
            Customer::class,
            Configuration::class,
        ],
    ],

];
