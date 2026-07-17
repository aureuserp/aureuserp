<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = TransferResource::class;
}
