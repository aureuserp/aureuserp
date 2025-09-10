<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Dropships\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\Dropships\DropshipResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Pages\ManageMoves as OperationManageMoves;

class ManageMoves extends OperationManageMoves
{
    protected static string $resource = DropshipResource::class;
}
