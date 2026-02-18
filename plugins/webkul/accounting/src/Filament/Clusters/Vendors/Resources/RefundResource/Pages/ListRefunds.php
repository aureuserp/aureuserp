<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource\Pages;

use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as BaseListInvoices;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource;

class ListRefunds extends BaseListInvoices
{
    protected static string $resource = RefundResource::class;
}
