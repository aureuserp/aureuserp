<?php

namespace Webkul\Asset\Filament\Resources\AssetMovementResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Filament\Resources\AssetMovementResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListAssetMovements extends ListRecords
{
    use HasTableViews;

    protected static string $resource = AssetMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getPresetTableViews(): array
    {
        return [
            'all' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.all'))
                ->icon('heroicon-s-bars-3')
                ->favorite()
                ->setAsDefault(),

            'assets' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.assets'))
                ->icon('heroicon-s-cube')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('asset.product', fn (Builder $q) => $q->where('type', AssetProductType::Asset))),

            'consumables' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.consumables'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('asset.product', fn (Builder $q) => $q->where('type', AssetProductType::Consumable))),

            'issues' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.issues'))
                ->icon('heroicon-s-arrow-up-right')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', MovementType::Issued)),

            'returns' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.returns'))
                ->icon('heroicon-s-arrow-down-left')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', MovementType::Returned)),

            'transfers' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.transfers'))
                ->icon('heroicon-s-arrows-right-left')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', MovementType::Transferred)),

            'scrapped_lost' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.scrapped-lost'))
                ->icon('heroicon-s-trash')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('type', [MovementType::Scrapped, MovementType::Lost])),

            'damaged' => PresetView::make(__('assets::filament/resources/asset-movement.pages.list.tabs.damaged'))
                ->icon('heroicon-s-exclamation-triangle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('condition', [AssetCondition::Damaged, AssetCondition::Poor])),
        ];
    }
}
