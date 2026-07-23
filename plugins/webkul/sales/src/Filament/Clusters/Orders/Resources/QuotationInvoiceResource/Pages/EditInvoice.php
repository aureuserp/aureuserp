<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\EditInvoice as BaseEditInvoice;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = QuotationInvoiceResource::class;
}
