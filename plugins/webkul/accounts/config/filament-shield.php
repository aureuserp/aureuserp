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

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            AccountResource::class => [...$basic, ...$delete],
            AccountTagResource::class => [...$basic, ...$delete],
            BankAccountResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            BillResource::class => [...$basic, ...$delete, ...$reorder],
            CashRoundingResource::class => [...$basic, ...$delete],
            CreditNoteResource::class => [...$basic, ...$delete, ...$reorder],
            CustomerResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            FiscalPositionResource::class => [...$basic, ...$delete, ...$reorder],
            IncotermResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            InvoiceResource::class => [...$basic, ...$delete, ...$reorder],
            JournalResource::class => [...$basic, ...$delete, ...$reorder],
            PartnerResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            PaymentResource::class => [...$basic, ...$delete],
            PaymentTermResource::class => [...$basic, ...$delete, ...$reorder],
            ProductCategoryResource::class => [...$basic, ...$delete],
            ProductResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            RefundResource::class => [...$basic, ...$delete, ...$reorder],
            TaxGroupResource::class => [...$basic, ...$delete, ...$reorder],
            TaxResource::class => [...$basic, ...$delete, ...$reorder],
            VendorResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
        ],
        'exclude' => [],
    ],
];
