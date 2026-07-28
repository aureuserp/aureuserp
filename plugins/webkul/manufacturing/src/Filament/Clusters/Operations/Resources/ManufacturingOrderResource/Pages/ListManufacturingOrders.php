<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Manufacturing\Enums\ManufacturingOrderReservationState;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListManufacturingOrders extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ManufacturingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'todo' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.todo'))
                ->icon('heroicon-s-squares-plus')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),

            'done' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.done'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', ManufacturingOrderState::DONE)),

            'cancelled' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.cancelled'))
                ->icon('heroicon-s-x-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', ManufacturingOrderState::CANCEL)),

            'planned' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.planned'))
                ->icon('heroicon-s-calendar-days')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_planned', true)),

            'draft' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.draft'))
                ->icon('heroicon-s-pencil-square')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', ManufacturingOrderState::DRAFT)),

            'confirmed' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.confirmed'))
                ->icon('heroicon-s-clipboard-document-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', ManufacturingOrderState::CONFIRMED)),

            'in-progress' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.in-progress'))
                ->icon('heroicon-s-arrow-path')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', ManufacturingOrderState::PROGRESS)),

            'to-close' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.to-close'))
                ->icon('heroicon-s-archive-box-arrow-down')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', ManufacturingOrderState::TO_CLOSE)),

            'mo-pending' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.mo-pending'))
                ->icon('heroicon-s-clock')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('reservation_state', [ManufacturingOrderReservationState::CONFIRMED, ManufacturingOrderReservationState::WAITING])),

            'mo-ready' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.mo-ready'))
                ->icon('heroicon-s-check-badge')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('reservation_state', ManufacturingOrderReservationState::ASSIGNED)),

            'my-mos' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.my-mos'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_user_id', Auth::id())),

            'late' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/list-manufacturing-orders.tabs.late'))
                ->icon('heroicon-s-exclamation-triangle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereNotNull('deadline_at')
                    ->where('deadline_at', '<', now())
                    ->whereNotIn('state', [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL])),
        ];
    }
}
