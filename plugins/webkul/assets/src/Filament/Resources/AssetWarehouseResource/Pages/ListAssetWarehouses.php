<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListAssetWarehouses extends ListRecords
{
    use HasTableViews;

    protected static string $resource = AssetWarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('assets::filament/resources/asset-warehouse.pages.list.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('assets::filament/resources/asset-warehouse.pages.list.header-actions.create.notification.title'))
                        ->body(__('assets::filament/resources/asset-warehouse.pages.list.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'all' => PresetView::make(__('assets::filament/resources/asset-warehouse.pages.list.views.all'))
                ->icon('heroicon-s-bars-3')
                ->favorite()
                ->setAsDefault(),

            'archived' => PresetView::make(__('assets::filament/resources/asset-warehouse.pages.list.views.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
