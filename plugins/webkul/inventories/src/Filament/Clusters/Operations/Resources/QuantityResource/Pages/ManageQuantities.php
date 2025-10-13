<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource\Pages;

use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageQuantities extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = QuantityResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('inventories::filament/clusters/operations/resources/quantity/pages/manage-quantities.title');
    }

    public function getPresetTableViews(): array
    {
        return [
            'internal_locations' => PresetView::make(__('inventories::filament/clusters/operations/resources/quantity/pages/manage-quantities.tabs.internal-locations'))
                ->favorite()
                ->icon('heroicon-s-building-office')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('location', function (Builder $query) {
                        $query->where('type', LocationType::INTERNAL);
                    });
                }),
            'transit_locations' => PresetView::make(__('inventories::filament/clusters/operations/resources/quantity/pages/manage-quantities.tabs.transit-locations'))
                ->favorite()
                ->icon('heroicon-s-truck')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('location', function (Builder $query) {
                        $query->where('type', LocationType::TRANSIT);
                    });
                }),
            'on_hand' => PresetView::make(__('inventories::filament/clusters/operations/resources/quantity/pages/manage-quantities.tabs.on-hand'))
                ->favorite()
                ->icon('heroicon-s-clipboard-document-list')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('quantity', '>', 0)),
            'to_count' => PresetView::make(__('inventories::filament/clusters/operations/resources/quantity/pages/manage-quantities.tabs.to-count'))
                ->favorite()
                ->icon('heroicon-s-calculator')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('scheduled_at', '>', now())),
            'to_apply' => PresetView::make(__('inventories::filament/clusters/operations/resources/quantity/pages/manage-quantities.tabs.to-apply'))
                ->favorite()
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('inventory_quantity_set', true)),
        ];
    }
}
