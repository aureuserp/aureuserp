<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageInvoices as BaseManageInvoices;

class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = OrderResource::class;
}
