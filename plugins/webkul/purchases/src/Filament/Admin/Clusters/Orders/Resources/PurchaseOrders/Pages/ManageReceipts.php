<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrders\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\ManageReceipts as BaseManageReceipts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrders\PurchaseOrderResource;

class ManageReceipts extends BaseManageReceipts
{
    protected static string $resource = PurchaseOrderResource::class;
}
