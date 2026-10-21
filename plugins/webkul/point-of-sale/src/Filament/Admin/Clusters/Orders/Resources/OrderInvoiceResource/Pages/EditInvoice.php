<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderInvoiceResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource\Pages\EditInvoice as BaseEditInvoice;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderInvoiceResource;

class EditInvoice extends BaseEditInvoice
{
    protected static string $resource = OrderInvoiceResource::class;
}
