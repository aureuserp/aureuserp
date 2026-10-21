<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource;

class ListPrinters extends ListRecords
{
    protected static string $resource = PrinterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer/pages/list-printers.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
