<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = QuotationInvoiceResource::class;
}
