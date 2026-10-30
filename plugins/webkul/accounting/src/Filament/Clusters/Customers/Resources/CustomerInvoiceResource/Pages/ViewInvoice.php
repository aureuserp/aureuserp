<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerInvoiceResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerInvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = CustomerInvoiceResource::class;
}
