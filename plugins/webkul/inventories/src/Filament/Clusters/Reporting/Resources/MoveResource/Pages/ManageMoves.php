<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources\MoveResource\Pages;

use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\MoveResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageMoves extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = MoveResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'todo_moves' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.tabs.todo'))
                ->favorite()
                ->icon('heroicon-o-clipboard-document-list')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [MoveState::DRAFT, MoveState::DONE, MoveState::CANCELED])),
            'done_moves' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.tabs.done'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::DONE)),
            'incoming_moves' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.tabs.incoming'))
                ->favorite()
                ->icon('heroicon-o-arrow-down-tray')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('operation.operationType', function (Builder $query) {
                        $query->where('type', OperationType::INCOMING);
                    });
                }),
            'outgoing_moves' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.tabs.outgoing'))
                ->favorite()
                ->icon('heroicon-o-arrow-up-tray')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('operation.operationType', function (Builder $query) {
                        $query->where('type', OperationType::OUTGOING);
                    });
                }),
            'internal_moves' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.tabs.internal'))
                ->favorite()
                ->icon('heroicon-o-arrows-right-left')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('operation.operationType', function (Builder $query) {
                        $query->where('type', OperationType::INTERNAL);
                    });
                }),
        ];
    }
}
