<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewFloor extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = FloorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
