<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = SaleCustomerInvoiceResource::class;
}
