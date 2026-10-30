<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages\ManageInvoices as BaseManageInvoices;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PosCustomerInvoiceResource;

class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = CustomerResource::class;

    protected static ?string $relatedResource = PosCustomerInvoiceResource::class;
}
