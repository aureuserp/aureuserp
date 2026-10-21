<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditFloor extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = FloorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
