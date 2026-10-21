<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPrinter extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PrinterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
