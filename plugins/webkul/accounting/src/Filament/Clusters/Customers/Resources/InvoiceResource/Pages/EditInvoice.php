<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice as BaseEditInvoice;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = InvoiceResource::class;

    protected static string $reverseResource = CreditNoteResource::class;
}
