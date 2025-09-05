<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\StorageCategoryResource;

class ViewStorageCategory extends ViewRecord
{
    protected static string $resource = StorageCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
