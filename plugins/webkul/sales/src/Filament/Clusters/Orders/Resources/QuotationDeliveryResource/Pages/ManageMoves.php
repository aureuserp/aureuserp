<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = QuotationDeliveryResource::class;
}
