<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = InvoiceResource::class;

    protected static string $reverseResource = CreditNoteResource::class;
}
