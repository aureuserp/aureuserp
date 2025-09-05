<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Receipts\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Pages\ManageMoves as OperationManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Receipts\ReceiptResource;

class ManageMoves extends OperationManageMoves
{
    protected static string $resource = ReceiptResource::class;
}
