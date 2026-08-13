<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\EditInvoice as BaseEditInvoice;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = OrderInvoiceResource::class;
}
