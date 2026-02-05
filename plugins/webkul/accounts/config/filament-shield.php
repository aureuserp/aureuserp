<?php

use Webkul\Account\Filament\Resources\AccountResource;
use Webkul\Account\Filament\Resources\AccountTagResource;
use Webkul\Account\Filament\Resources\BankAccountResource;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\CashRoundingResource;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\FiscalPositionResource;
use Webkul\Account\Filament\Resources\IncoTermResource;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\PaymentTermResource;
use Webkul\Account\Filament\Resources\ProductCategoryResource;
use Webkul\Account\Filament\Resources\ProductResource;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Account\Filament\Resources\TaxGroupResource;
use Webkul\Account\Filament\Resources\TaxResource;

return [
    'resources' => [
        'manage'  => [],
        'exclude' => [
            AccountResource::class,
            PaymentResource::class,
            InvoiceResource::class,
            BillResource::class,
            RefundResource::class,
            BankAccountResource::class,
            IncoTermResource::class,
            PaymentTermResource::class,
            TaxGroupResource::class,
            TaxResource::class,
            AccountTagResource::class,
            CashRoundingResource::class,
            CreditNoteResource::class,
            FiscalPositionResource::class,
            \Webkul\Account\Filament\Resources\IncotermResource::class,
            JournalResource::class,
            ProductCategoryResource::class,
            ProductResource::class,
            AccountTagResource::class,
            AccountTagResource::class,
            AccountTagResource::class,
        ],
    ],

];
