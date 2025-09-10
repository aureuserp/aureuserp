<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoices\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ViewQuotation as BaseViewQuotation;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoices\OrderToInvoiceResource;

class ViewOrderToInvoice extends BaseViewQuotation
{
    protected static string $resource = OrderToInvoiceResource::class;
}
