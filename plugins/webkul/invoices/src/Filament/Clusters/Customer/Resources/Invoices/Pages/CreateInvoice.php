<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\Pages;

use Webkul\Account\Filament\Resources\Invoices\Pages\CreateInvoice as BaseCreateInvoice;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\InvoiceResource;

class CreateInvoice extends BaseCreateInvoice
{
    protected static string $resource = InvoiceResource::class;
}
