<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource\Pages;

use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\OrderPointTrigger;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReplenishmentResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageReplenishment extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = ReplenishmentResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('inventories::filament/clusters/operations/resources/replenishment/pages/manage-replenishment.title');
    }

    public function getPresetTableViews(): array
    {
        return [
            'trigger_manual' => PresetView::make(__('inventories::filament/clusters/operations/resources/replenishment/pages/manage-replenishment.tabs.trigger-manual'))
                ->favorite()
                ->icon('heroicon-s-building-office')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('trigger', OrderPointTrigger::MANUAL)),

            'trigger_automatic' => PresetView::make(__('inventories::filament/clusters/operations/resources/replenishment/pages/manage-replenishment.tabs.trigger-automatic'))
                ->favorite()
                ->icon('heroicon-s-building-office')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('trigger', OrderPointTrigger::AUTOMATIC)),
        ];
    }
}
