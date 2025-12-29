<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice as BaseCreateInvoice;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource;

class CreateInvoice extends BaseCreateInvoice
{
    protected static string $resource = InvoiceResource::class;
}
