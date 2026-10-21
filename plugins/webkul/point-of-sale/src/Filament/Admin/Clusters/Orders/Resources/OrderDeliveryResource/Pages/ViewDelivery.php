<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ViewDelivery as BaseViewDelivery;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource;

class ViewDelivery extends BaseViewDelivery
{
    protected static string $resource = OrderDeliveryResource::class;
}
