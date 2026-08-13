<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Models\WorkOrder;

class WorkOrdersTable
{
    public static function configure(Table $table, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->reorderableColumns()
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('manufacturingOrder')
                ->orderBy('sort')
                ->orderBy('calendar_leave_id')
                ->orderBy('started_at')
                ->orderBy('id'))
            ->columns(array_merge([
                TextColumn::make('operation.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.operation'))
                    ->searchable(),
                TextColumn::make('workCenter.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.work-center'))
                    ->searchable(),
                TextColumn::make('manufacturing_order_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.manufacturing-order'))
                    ->placeholder('—')
                    ->formatStateUsing(fn (mixed $state, WorkOrder $record): string => WorkOrderResource::getManufacturingOrderLabel($record->manufacturingOrder))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('manufacturingOrder', function (Builder $relationQuery) use ($search): void {
                            $relationQuery->whereLike('name', "%{$search}%")
                                ->orWhereLike('reference', "%{$search}%");
                        });
                    }),
                TextColumn::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.product'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('quantity_remaining')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.quantity-remaining'))
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(),
                TextColumn::make('manufacturingOrder.producingLot.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.lot-serial'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.start'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.end'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expected_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.expected-duration'))
                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes'))
                    ->summarize([
                        Sum::make()
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.expected-duration'))
                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                    ]),
                TextColumn::make('duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.real-duration'))
                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes'))
                    ->summarize([
                        Sum::make()
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.real-duration'))
                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                    ]),
                TextColumn::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.columns.status'))
                    ->badge(),
            ], $customColumns))
            ->groups([
                TableGroup::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.status')),
                TableGroup::make('workCenter.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.work-center')),
                TableGroup::make('manufacturingOrder.reference')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.manufacturing-order')),
                TableGroup::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.product')),
                TableGroup::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.start'))
                    ->date(),
                TableGroup::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.groups.end'))
                    ->date(),
            ])
            ->filters(array_merge([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.work-order')),
                        SelectConstraint::make('state')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.status'))
                            ->multiple()
                            ->options(WorkOrderState::class)
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('operation')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.operation'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-cog-6-tooth'),
                        RelationshipConstraint::make('workCenter')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.work-center'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-cog-6-tooth'),
                        RelationshipConstraint::make('manufacturingOrder')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.manufacturing-order'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->getOptionLabelFromRecordUsing(fn (Order $record): string => WorkOrderResource::getManufacturingOrderLabel($record))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-wrench-screwdriver'),
                        RelationshipConstraint::make('product')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.product'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-cube'),
                        DateConstraint::make('started_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.start')),
                        DateConstraint::make('finished_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.end')),
                        DateConstraint::make('created_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.table.filters.updated-at')),
                    ]),
            ], $customFilters), layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                Action::make('button_start')
                    ->hiddenLabel()
                    ->icon('heroicon-m-play-circle')
                    ->color('success')
                    ->size(Size::ExtraLarge)
                    ->visible(function (WorkOrder $record): bool {
                        $productionState = $record->manufacturingOrder?->state;

                        return ! in_array($productionState, [
                            ManufacturingOrderState::DRAFT,
                            ManufacturingOrderState::DONE,
                            ManufacturingOrderState::CANCEL,
                        ], true)
                            && $record->working_state !== WorkCenterWorkingState::BLOCKED
                            && ! in_array($record->state, [WorkOrderState::DONE, WorkOrderState::CANCEL], true)
                            && ! $record->is_user_working;
                    })
                    ->databaseTransaction()
                    ->action(function (WorkOrder $record): void {
                        $record->start();
                        $record->refresh();
                    }),
                Action::make('button_pending')
                    ->hiddenLabel()
                    ->icon('heroicon-m-pause-circle')
                    ->color('warning')
                    ->size(Size::ExtraLarge)
                    ->visible(function (WorkOrder $record): bool {
                        $productionState = $record->manufacturingOrder?->state;

                        return ! in_array($productionState, [
                            ManufacturingOrderState::DRAFT,
                            ManufacturingOrderState::DONE,
                            ManufacturingOrderState::CANCEL,
                        ], true)
                            && $record->working_state !== WorkCenterWorkingState::BLOCKED
                            && $record->is_user_working;
                    })
                    ->databaseTransaction()
                    ->action(function (WorkOrder $record): void {
                        $record->pending();
                        $record->refresh();
                    }),
                Action::make('button_done')
                    ->hiddenLabel()
                    ->icon('heroicon-m-check-circle')
                    ->color('primary')
                    ->size(Size::ExtraLarge)
                    ->visible(function (WorkOrder $record): bool {
                        $productionState = $record->manufacturingOrder?->state;

                        return ! in_array($productionState, [
                            ManufacturingOrderState::DRAFT,
                            ManufacturingOrderState::DONE,
                            ManufacturingOrderState::CANCEL,
                        ], true)
                            && $record->working_state !== WorkCenterWorkingState::BLOCKED
                            && $record->is_user_working;
                    })
                    ->databaseTransaction()
                    ->action(function (WorkOrder $record): void {
                        $record->finish();
                        $record->refresh();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([]);
    }
}
