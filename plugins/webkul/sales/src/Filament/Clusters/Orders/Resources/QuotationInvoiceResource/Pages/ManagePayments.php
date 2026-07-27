<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = QuotationInvoiceResource::class;
}
