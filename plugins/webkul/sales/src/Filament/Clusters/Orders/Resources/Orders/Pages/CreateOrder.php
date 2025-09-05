<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\CreateQuotation as BaseCreateOrders;

class CreateOrder extends BaseCreateOrders
{
    protected static string $resource = OrderResource::class;
}
