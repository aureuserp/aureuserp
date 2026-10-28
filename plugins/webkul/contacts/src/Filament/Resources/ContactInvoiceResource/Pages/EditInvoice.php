<?php

namespace Webkul\Contact\Filament\Resources\ContactInvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice as BaseEditInvoice;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = ContactInvoiceResource::class;
}
