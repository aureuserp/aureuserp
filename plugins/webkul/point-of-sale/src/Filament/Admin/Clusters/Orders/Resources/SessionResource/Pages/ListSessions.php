<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListSessions extends ListRecords
{
    use HasTableViews;

    protected static string $resource = SessionResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'open' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/session/pages/list-sessions.tabs.open'))
                ->icon('heroicon-s-play-circle')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    SessionState::OPENING_CONTROL,
                    SessionState::OPENED,
                    SessionState::CLOSING_CONTROL,
                ])),

            'closing-control' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/session/pages/list-sessions.tabs.closing-control'))
                ->icon('heroicon-s-calculator')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', SessionState::CLOSING_CONTROL)),

            'closed' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/session/pages/list-sessions.tabs.closed'))
                ->icon('heroicon-s-lock-closed')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', SessionState::CLOSED)),

            'rescue' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/session/pages/list-sessions.tabs.rescue'))
                ->icon('heroicon-s-lifebuoy')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_rescue', true)),
        ];
    }
}
