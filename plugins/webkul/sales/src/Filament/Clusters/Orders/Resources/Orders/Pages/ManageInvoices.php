<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ManageInvoices as BaseManageInvoices;

class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = OrderResource::class;
}
