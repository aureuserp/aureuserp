<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = PosCustomerInvoiceResource::class;
}
