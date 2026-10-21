<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewCategory extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
