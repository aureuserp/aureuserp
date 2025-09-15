<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListScraps extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ScrapResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('inventories::filament/clusters/operations/resources/scrap.navigation.title');
    }

    public function getPresetTableViews(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/operations/resources/scrap/pages/list-scraps.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
