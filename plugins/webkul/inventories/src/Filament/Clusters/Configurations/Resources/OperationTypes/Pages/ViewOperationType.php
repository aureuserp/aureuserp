<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypes\OperationTypeResource;

class ViewOperationType extends ViewRecord
{
    protected static string $resource = OperationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
