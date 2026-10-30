<?php

namespace Webkul\Contact\Filament\Resources\ContactInvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = ContactInvoiceResource::class;
}
