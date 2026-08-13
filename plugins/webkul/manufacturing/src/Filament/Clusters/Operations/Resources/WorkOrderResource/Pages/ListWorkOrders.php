<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListWorkOrders extends ListRecords
{
    use HasTableViews;

    protected static string $resource = WorkOrderResource::class;

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/work-order/pages/list-work-orders.title');
    }

    public function getPresetTableViews(): array
    {
        return [
            'todo' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/work-order/pages/list-work-orders.tabs.todo'))
                ->icon('heroicon-s-squares-plus')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [WorkOrderState::PROGRESS, WorkOrderState::READY, WorkOrderState::WAITING, WorkOrderState::PENDING])),

            'draft' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/work-order/pages/list-work-orders.tabs.draft'))
                ->icon('heroicon-s-pencil-square')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', WorkOrderState::PENDING)->whereHas('manufacturingOrder', fn (Builder $q) => $q->where('state', ManufacturingOrderState::DRAFT))),

            'done' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/work-order/pages/list-work-orders.tabs.done'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', WorkOrderState::DONE)),

            'cancelled' => PresetView::make(__('manufacturing::filament/clusters/operations/resources/work-order/pages/list-work-orders.tabs.cancelled'))
                ->icon('heroicon-s-x-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', WorkOrderState::CANCEL)),
        ];
    }
}
