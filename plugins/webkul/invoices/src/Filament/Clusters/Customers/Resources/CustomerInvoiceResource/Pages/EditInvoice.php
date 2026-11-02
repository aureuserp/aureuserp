<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerInvoiceResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\EditInvoice as BaseEditInvoice;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = CustomerInvoiceResource::class;
}
