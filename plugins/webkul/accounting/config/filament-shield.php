<?php

use Webkul\Accounting\Filament\Clusters\Accounting;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\AccountResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\IncotermResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\JournalResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\PaymentTermResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;
use Webkul\Accounting\Filament\Clusters\Customers;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\PaymentResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\ProductResource;
use Webkul\Accounting\Filament\Clusters\Vendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource as AccountingPaymentResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource as AccountingProductResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Accounting\Filament\Widgets\JournalChartsWidget;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            JournalEntryResource::class => $permissions['REORDER'],
            JournalItemResource::class => $permissions['REORDER'],
            AccountResource::class => $permissions['BASIC'],
            CashRoundingResource::class => $permissions['BASIC'],
            CurrencyResource::class => $permissions['BASIC'],
            FiscalPositionResource::class => $permissions['REORDER'],
            IncotermResource::class => $permissions['SOFT_DELETE'],
            JournalResource::class => $permissions['REORDER'],
            PaymentTermResource::class => $permissions['FULL'],
            ProductAttributeResource::class => $permissions['FULL'],
            ProductCategoryResource::class => $permissions['BASIC'],
            TaxGroupResource::class => $permissions['REORDER'],
            TaxResource::class => $permissions['REORDER'],
            CreditNoteResource::class => $permissions['REORDER'],
            CustomerResource::class => $permissions['SOFT_DELETE'],
            InvoiceResource::class => $permissions['REORDER'],
            PaymentResource::class => $permissions['BASIC'],
            ProductResource::class => $permissions['FULL'],
            BillResource::class => $permissions['REORDER'],
            RefundResource::class => $permissions['REORDER'],
            VendorResource::class => $permissions['SOFT_DELETE'],
        ],
        'exclude' => [
            AccountingProductResource::class,
            AccountingPaymentResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Vendors::class,
            Customers::class,
            Accounting::class,
            Configuration::class,
        ],
    ],

    'widgets' => [
        'exclude' => [
            JournalChartsWidget::class,
        ],
    ],

];
