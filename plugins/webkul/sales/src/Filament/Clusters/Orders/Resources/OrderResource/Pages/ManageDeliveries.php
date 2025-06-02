<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageDeliveries as BaseManageDeliveries;

class ManageDeliveries extends BaseManageDeliveries
{
    protected static string $resource = OrderResource::class;
}
