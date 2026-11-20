<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;
use Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = SaleCustomerInvoiceResource::class;
}
