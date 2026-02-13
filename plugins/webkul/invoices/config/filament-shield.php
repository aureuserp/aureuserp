<?php

use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\BankAccountResource;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\IncoTermResource;
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
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;

return [
    'resources' => [
        'manage' => [
            CustomerResource::class                                                                   => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            PaymentResource::class                                                                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            CreditNoteResource::class                                                                 => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            InvoiceResource::class                                                                    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource::class                => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            BillResource::class                                                                       => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            VendorResource::class                                                                     => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            RefundResource::class                                                                     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            BankAccountResource::class                                                                => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            IncoTermResource::class                                                                   => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            PaymentTermResource::class                                                                => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            ProductCategoryResource::class                                                            => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            ProductAttributeResource::class                                                           => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            TaxGroupResource::class                                                                   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            TaxResource::class                                                                        => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
        ],
        'exclude' => [
            ProductResource::class,
            \Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentResource::class,
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
