<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerInvoiceResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = CustomerInvoiceResource::class;
}
