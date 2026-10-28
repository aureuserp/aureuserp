<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerInvoiceResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerInvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ManagePayments as BaseManagePayments;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = CustomerInvoiceResource::class;
}
