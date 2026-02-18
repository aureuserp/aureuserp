<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as BaseListInvoices;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;

class ListInvoices extends BaseListInvoices
{
    protected static string $resource = InvoiceResource::class;
}
