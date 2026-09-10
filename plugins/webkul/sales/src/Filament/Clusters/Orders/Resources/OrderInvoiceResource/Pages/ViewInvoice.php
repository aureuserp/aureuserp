<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\ViewInvoice as BaseViewInvoice;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = OrderInvoiceResource::class;
}
