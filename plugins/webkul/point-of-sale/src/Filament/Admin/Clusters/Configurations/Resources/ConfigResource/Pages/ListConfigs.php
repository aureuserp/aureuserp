<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListConfigs extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/list-configs.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/list-configs.header-actions.create.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/list-configs.header-actions.create.notification.success.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'active' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/list-configs.tabs.active'))
                ->icon('heroicon-s-computer-desktop')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed()->where('is_active', true)),

            'restaurant' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/list-configs.tabs.restaurant'))
                ->icon('heroicon-s-building-storefront')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed()->where('is_restaurant', true)),

            'archived' => PresetView::make(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/list-configs.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
