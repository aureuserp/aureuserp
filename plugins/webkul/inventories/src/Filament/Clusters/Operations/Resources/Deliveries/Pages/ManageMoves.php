<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Deliveries\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\Deliveries\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Pages\ManageMoves as OperationManageMoves;

class ManageMoves extends OperationManageMoves
{
    protected static string $resource = DeliveryResource::class;
}
