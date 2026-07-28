<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\ViewDelivery as BaseViewDelivery;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource;

class ViewDelivery extends BaseViewDelivery
{
    protected static string $resource = OrderDeliveryResource::class;
}
