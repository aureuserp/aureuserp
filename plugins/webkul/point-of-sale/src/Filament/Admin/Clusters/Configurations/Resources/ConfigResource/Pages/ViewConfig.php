<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Actions\OpenTerminalAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewConfig extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            OpenTerminalAction::make(),

            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
