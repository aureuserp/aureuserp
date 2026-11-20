<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\EditInvoice as BaseEditInvoice;
use Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = SaleCustomerInvoiceResource::class;
}
