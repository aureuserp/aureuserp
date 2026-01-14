<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageMoves extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;
    use HasTableViews;

    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'moveLines';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/product/pages/manage-moves.title');
    }

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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.product'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible((bool) $this->getOwnerRecord()->is_configurable),
                TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.date'))
                    ->sortable()
                    ->dateTime(),
                TextColumn::make('reference')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.lot'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers && $this->getOwnerRecord()->tracking != ProductTracking::QTY),
                TextColumn::make('resultPackage.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.package'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.source-location'))
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.destination-location'))
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('uom_qty')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.quantity'))
                    ->sortable()
                    ->color(fn ($record) => $record->destinationLocation->type == LocationType::INTERNAL ? 'success' : 'danger'),
                TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.done-by'))
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.actions.delete.notification.body')),
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('state', MoveState::DONE);
            });
    }
}
