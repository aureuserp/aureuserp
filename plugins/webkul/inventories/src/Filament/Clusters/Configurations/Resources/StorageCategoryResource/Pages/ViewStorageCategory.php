<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewStorageCategory extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = StorageCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
