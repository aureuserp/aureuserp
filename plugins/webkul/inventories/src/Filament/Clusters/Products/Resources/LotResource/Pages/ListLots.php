<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListLots extends ListRecords
{
    use HasTableViews;

    protected static string $resource = LotResource::class;

    public function getPresetTableViews(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/products/resources/lot/pages/list-lots.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
