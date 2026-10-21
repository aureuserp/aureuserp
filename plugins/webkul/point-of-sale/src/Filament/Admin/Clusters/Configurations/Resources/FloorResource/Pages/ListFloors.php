<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListFloors extends ListRecords
{
    use HasTableViews;

    protected static string $resource = FloorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/pages/list-floors.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'active' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/pages/list-floors.tabs.active'))
                ->icon('heroicon-s-squares-2x2')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed()),

            'archived' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/pages/list-floors.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
