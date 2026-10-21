<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\EditDelivery as BaseEditDelivery;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource;

class EditDelivery extends BaseEditDelivery
{
    protected static string $resource = OrderDeliveryResource::class;
}
