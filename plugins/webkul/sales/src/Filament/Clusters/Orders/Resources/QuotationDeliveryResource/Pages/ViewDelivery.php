<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ViewDelivery as BaseViewDelivery;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Concerns\ReplacesNextTransferAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource;

class ViewDelivery extends BaseViewDelivery
{
    use ReplacesNextTransferAction;

    protected static string $resource = QuotationDeliveryResource::class;
}
