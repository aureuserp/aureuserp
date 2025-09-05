<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Operations\OperationResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Settings\WarehouseSettings;

class OperationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_favorite')
                    ->label('')
                    ->icon(fn (Operation $record): string => $record->is_favorite ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Operation $record): string => $record->is_favorite ? 'warning' : 'gray')
                    ->action(function (Operation $record): void {
                        $record->update([
                            'is_favorite' => ! $record->is_favorite,
                        ]);
                    }),
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.from'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.to'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations),
                TextColumn::make('partner.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.contact'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.responsible'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.scheduled-at'))
                    ->placeholder('—')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('deadline')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.deadline'))
                    ->placeholder('—')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('closed_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.closed-at'))
                    ->placeholder('—')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('origin')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.source-document'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('operationType.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.operation-type'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.company'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.columns.state'))
                    ->sortable()
                    ->badge(),
            ])
            ->groups([
                Group::make('state')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.state')),
                Group::make('origin')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.source-document')),
                Group::make('operationType.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.operation-type')),
                Group::make('schedule_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.scheduled-at'))
                    ->date(),
                Group::make('created_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect(OperationResource::mergeCustomTableQueryBuilderConstraints([
                        TextConstraint::make('name')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.name')),
                        SelectConstraint::make('state')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.state'))
                            ->multiple()
                            ->options(OperationState::class)
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('partner')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.partner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.responsible'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('owner')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.owner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        app(WarehouseSettings::class)->enable_locations
                        ? RelationshipConstraint::make('sourceLocation')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.source-location'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('full_name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-map-pin')
                        : null,
                        app(WarehouseSettings::class)->enable_locations
                        ? RelationshipConstraint::make('destinationLocation')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.destination-location'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('full_name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-map-pin')
                        : null,
                        DateConstraint::make('deadline')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.deadline'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('scheduled_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.scheduled-at')),
                        DateConstraint::make('closed_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.closed-at')),
                        DateConstraint::make('created_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.updated-at')),
                        RelationshipConstraint::make('company')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('creator')
                            ->label(__('inventories::filament/clusters/operations/resources/operation.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ]))->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => OperationResource::can('delete', $record) && $record->state !== OperationState::DONE,
            );
    }
}
