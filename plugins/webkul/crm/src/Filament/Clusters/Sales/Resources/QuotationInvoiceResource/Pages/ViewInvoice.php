<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\ViewInvoice as BaseViewInvoice;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = QuotationInvoiceResource::class;
}
