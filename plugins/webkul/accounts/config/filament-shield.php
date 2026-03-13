<?php

use Webkul\Account\Filament\Resources\AccountResource;
use Webkul\Account\Filament\Resources\AccountTagResource;
use Webkul\Account\Filament\Resources\BankAccountResource;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\CashRoundingResource;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\FiscalPositionResource;
use Webkul\Account\Filament\Resources\IncotermResource;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Filament\Resources\PartnerResource;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\PaymentTermResource;
use Webkul\Account\Filament\Resources\ProductCategoryResource;
use Webkul\Account\Filament\Resources\ProductResource;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Account\Filament\Resources\TaxGroupResource;
use Webkul\Account\Filament\Resources\TaxResource;
use Webkul\Account\Filament\Resources\VendorResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            AccountResource::class => $permissions['BASIC'],
            AccountTagResource::class => $permissions['BASIC'],
            BankAccountResource::class => $permissions['SOFT_DELETE'],
            BillResource::class => $permissions['REORDER'],
            CashRoundingResource::class => $permissions['BASIC'],
            CreditNoteResource::class => $permissions['REORDER'],
            CustomerResource::class => $permissions['SOFT_DELETE'],
            FiscalPositionResource::class => $permissions['REORDER'],
            IncotermResource::class => $permissions['SOFT_DELETE'],
            InvoiceResource::class => $permissions['REORDER'],
            JournalResource::class => $permissions['REORDER'],
            PartnerResource::class => $permissions['SOFT_DELETE'],
            PaymentResource::class => $permissions['BASIC'],
            PaymentTermResource::class => $permissions['REORDER'],
            ProductCategoryResource::class => $permissions['BASIC'],
            ProductResource::class => $permissions['FULL'],
            RefundResource::class => $permissions['REORDER'],
            TaxGroupResource::class => $permissions['REORDER'],
            TaxResource::class => $permissions['REORDER'],
            VendorResource::class => $permissions['SOFT_DELETE'],
        ],
        'exclude' => [],
    ],
];
