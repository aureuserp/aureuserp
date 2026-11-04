<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\EditInvoice as BaseEditInvoice;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = QuotationInvoiceResource::class;
}
