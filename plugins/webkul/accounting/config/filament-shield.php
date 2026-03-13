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

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            JournalEntryResource::class => [...$basic, ...$delete, ...$reorder],
            JournalItemResource::class => [...$basic, ...$delete, ...$reorder],
            AccountResource::class => [...$basic, ...$delete],
            CashRoundingResource::class => [...$basic, ...$delete],
            CurrencyResource::class => [...$basic, ...$delete],
            FiscalPositionResource::class => [...$basic, ...$delete, ...$reorder],
            IncotermResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            JournalResource::class => [...$basic, ...$delete, ...$reorder],
            PaymentTermResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ProductAttributeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ProductCategoryResource::class => [...$basic, ...$delete],
            TaxGroupResource::class => [...$basic, ...$delete, ...$reorder],
            TaxResource::class => [...$basic, ...$delete, ...$reorder],
            CreditNoteResource::class => [...$basic, ...$delete, ...$reorder],
            CustomerResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            InvoiceResource::class => [...$basic, ...$delete, ...$reorder],
            PaymentResource::class => [...$basic, ...$delete],
            ProductResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            BillResource::class => [...$basic, ...$delete, ...$reorder],
            RefundResource::class => [...$basic, ...$delete, ...$reorder],
            VendorResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
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
