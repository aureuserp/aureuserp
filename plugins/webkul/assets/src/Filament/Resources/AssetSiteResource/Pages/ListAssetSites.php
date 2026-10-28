<?php

namespace Webkul\Asset\Filament\Resources\AssetSiteResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListAssetSites extends ListRecords
{
    use HasTableViews;

    protected static string $resource = AssetSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('assets::filament/resources/asset-site.pages.list.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('assets::filament/resources/asset-site.pages.list.header-actions.create.notification.title'))
                        ->body(__('assets::filament/resources/asset-site.pages.list.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'all' => PresetView::make(__('assets::filament/resources/asset-site.pages.list.views.all'))
                ->icon('heroicon-s-bars-3')
                ->favorite()
                ->setAsDefault(),

            'asset_locations' => PresetView::make(__('assets::filament/resources/asset-site.pages.list.views.asset-locations'))
                ->icon('heroicon-s-building-office-2')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('usage_type', AssetSiteUsage::AssetLocation)),

            'custody_zones' => PresetView::make(__('assets::filament/resources/asset-site.pages.list.views.custody-zones'))
                ->icon('heroicon-s-user-group')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('usage_type', AssetSiteUsage::CustodyZone)),

            'archived' => PresetView::make(__('assets::filament/resources/asset-site.pages.list.views.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
