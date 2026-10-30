<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageInvoices as BaseManageInvoices;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\SaleCustomerInvoiceResource;

class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $relatedResource = SaleCustomerInvoiceResource::class;
}
