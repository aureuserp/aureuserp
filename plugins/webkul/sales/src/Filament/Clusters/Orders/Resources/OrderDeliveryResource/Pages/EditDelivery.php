<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\EditDelivery as BaseEditDelivery;

class EditDelivery extends BaseEditDelivery
{
    protected static string $resource = OrderDeliveryResource::class;
}
