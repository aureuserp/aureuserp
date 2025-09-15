<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPackages extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PackageResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'internal_locations' => PresetView::make(__('inventories::filament/clusters/products/resources/package/pages/list-packages.tabs.internal'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-map-pin')
                ->modifyQueryUsing(function ($query) {
                    return $query->whereHas('location', function (Builder $query) {
                        $query->where('type', LocationType::INTERNAL);
                    });
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/products/resources/package/pages/list-packages.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
