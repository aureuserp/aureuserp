<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPrinter extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PrinterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
