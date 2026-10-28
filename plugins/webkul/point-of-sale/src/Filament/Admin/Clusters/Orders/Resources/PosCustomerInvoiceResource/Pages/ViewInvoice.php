<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\ViewInvoice as BaseViewInvoice;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource;

class ViewInvoice extends BaseViewInvoice
{
    protected static string $resource = PosCustomerInvoiceResource::class;
}
