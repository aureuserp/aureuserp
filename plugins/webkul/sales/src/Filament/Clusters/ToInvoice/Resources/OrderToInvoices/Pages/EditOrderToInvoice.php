<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoices\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\EditQuotation as BaseEditQuotation;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoices\OrderToInvoiceResource;

class EditOrderToInvoice extends BaseEditQuotation
{
    protected static string $resource = OrderToInvoiceResource::class;
}
