<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = OrderDeliveryResource::class;
}
