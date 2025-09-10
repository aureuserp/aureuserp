<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ViewQuotation as BaseViewOrders;

class ViewOrder extends BaseViewOrders
{
    protected static string $resource = OrderResource::class;
}
