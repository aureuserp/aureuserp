<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice as BaseCreateInvoice;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;

class CreateInvoice extends BaseCreateInvoice
{
    protected static string $resource = InvoiceResource::class;
}
