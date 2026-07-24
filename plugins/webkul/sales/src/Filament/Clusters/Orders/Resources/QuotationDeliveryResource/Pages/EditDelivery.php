<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\EditDelivery as BaseEditDelivery;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Concerns\ReplacesNextTransferAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource;

class EditDelivery extends BaseEditDelivery
{
    use ReplacesNextTransferAction;

    protected static string $resource = QuotationDeliveryResource::class;
}
