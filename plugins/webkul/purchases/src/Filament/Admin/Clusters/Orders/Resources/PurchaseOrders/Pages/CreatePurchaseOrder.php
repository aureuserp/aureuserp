<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrders\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\CreateOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrders\PurchaseOrderResource;

class CreatePurchaseOrder extends CreateOrder
{
    protected static string $resource = PurchaseOrderResource::class;
}
