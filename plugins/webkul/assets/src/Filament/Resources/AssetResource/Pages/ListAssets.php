<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\BulkRegisterAssetsAction;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListAssets extends ListRecords
{
    use HasTableViews;

    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('assets::filament/resources/asset.pages.list.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('assets::filament/resources/asset.pages.list.header-actions.create.notification.title'))
                        ->body(__('assets::filament/resources/asset.pages.list.header-actions.create.notification.body')),
                ),
            BulkRegisterAssetsAction::make(),
        ];
    }

    public function getPresetTableViews(): array
    {
        return array_filter([
            'all' => PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.all'))
                ->icon('heroicon-s-bars-3')
                ->favorite()
                ->setAsDefault(),

            'available' => PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.available'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Available)),

            'consumables' => PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.consumables'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('product', fn (Builder $q) => $q->where('type', AssetProductType::Consumable))),

            'site_assets' => app(AssetSettings::class)->enable_site_assets
                ? PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.site-assets'))
                    ->icon('heroicon-s-building-office-2')
                    ->favorite()
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Held)->whereNull('current_employee_id')->whereNotNull('current_site_id'))
                : null,

            'with_employees' => PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.with-employees'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Held)->whereNotNull('current_employee_id')),

            'overdue_return' => PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.overdue-return'))
                ->icon('heroicon-s-clock')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', AssetStatus::Held)->whereNotNull('expected_return_at')->where('expected_return_at', '<', now()->startOfDay())),

            'scrapped_lost' => PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.scrapped-lost'))
                ->icon('heroicon-s-trash')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [AssetStatus::Scrapped, AssetStatus::Lost])),

            'archived' => PresetView::make(__('assets::filament/resources/asset.pages.list.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ]);
    }
}
