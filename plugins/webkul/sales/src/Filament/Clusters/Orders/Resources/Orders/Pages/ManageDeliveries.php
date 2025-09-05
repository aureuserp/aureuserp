<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\Orders\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ManageDeliveries as BaseManageDeliveries;

class ManageDeliveries extends BaseManageDeliveries
{
    protected static string $resource = OrderResource::class;
}
