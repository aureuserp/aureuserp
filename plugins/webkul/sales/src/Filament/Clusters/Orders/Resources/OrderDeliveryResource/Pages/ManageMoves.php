<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderDeliveryResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\ManageMoves as BaseManageMoves;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = OrderDeliveryResource::class;
}
