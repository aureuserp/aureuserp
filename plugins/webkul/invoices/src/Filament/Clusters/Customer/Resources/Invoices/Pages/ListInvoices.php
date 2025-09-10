<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\Pages;

use Webkul\Account\Filament\Resources\Invoices\Pages\ListInvoices as BaseListInvoices;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\InvoiceResource;

class ListInvoices extends BaseListInvoices
{
    protected static string $resource = InvoiceResource::class;
}
