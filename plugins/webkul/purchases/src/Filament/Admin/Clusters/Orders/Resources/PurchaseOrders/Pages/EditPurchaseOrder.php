<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrders\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\EditOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrders\PurchaseOrderResource;

class EditPurchaseOrder extends EditOrder
{
    protected static string $resource = PurchaseOrderResource::class;
}
