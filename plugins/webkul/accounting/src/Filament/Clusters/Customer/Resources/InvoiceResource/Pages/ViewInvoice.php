<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CreditNoteResource;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = InvoiceResource::class;

    protected static string $reverseResource = CreditNoteResource::class;
}
