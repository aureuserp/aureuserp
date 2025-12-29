<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as BaseListInvoices;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\InvoiceResource;

class ListInvoices extends BaseListInvoices
{
    protected static string $resource = InvoiceResource::class;
}
