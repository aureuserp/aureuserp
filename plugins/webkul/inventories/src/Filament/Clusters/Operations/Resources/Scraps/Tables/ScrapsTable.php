<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\ScrapResource;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class ScrapsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('closed_at')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.date'))
                    ->sortable()
                    ->date(),
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.lot'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.package'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.source-location'))
                    ->sortable()
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.scrap-location'))
                    ->sortable()
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('qty')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.quantity'))
                    ->sortable(),
                TextColumn::make('uom.name')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.uom'))
                    ->sortable()
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/operations/resources/scrap.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(
                collect([
                    Tables\Grouping\Group::make('product.name')
                        ->label(__('inventories::filament/clusters/operations/resources/scrap.table.groups.product')),
                    Tables\Grouping\Group::make('sourceLocation.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/scrap.table.groups.source-location')),
                    Tables\Grouping\Group::make('destinationLocation.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/scrap.table.groups.destination-location')),
                ])->filter(function ($group) {
                    return match ($group->getId()) {
                        'sourceLocation.full_name', 'destinationLocation.full_name' => app(WarehouseSettings::class)->enable_locations,
                        default => true
                    };
                })->all()
            )
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect([
                        app(WarehouseSettings::class)->enable_locations
                            ? RelationshipConstraint::make('sourceLocation')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.source-location'))
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
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.destination-location'))
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
                        SelectConstraint::make('state')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.state'))
                            ->multiple()
                            ->options(OperationState::class)
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('product')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.product'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-shopping-bag'),
                        app(ProductSettings::class)->enable_uom
                            ? RelationshipConstraint::make('uom')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.uom'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-shopping-bag')
                            : null,
                        RelationshipConstraint::make('product.category')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.product-category'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('full_name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-folder'),
                        app(TraceabilitySettings::class)->enable_lots_serial_numbers
                            ? RelationshipConstraint::make('lot')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.lot'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-rectangle-stack')
                            : null,
                        app(OperationSettings::class)->enable_packages
                            ? RelationshipConstraint::make('package')
                                ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.package'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-cube')
                            : null,
                        NumberConstraint::make('qty')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.quantity'))
                            ->icon('heroicon-o-scale'),
                        DateConstraint::make('closed_at')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.closed-at')),
                        DateConstraint::make('created_at')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.updated-at')),
                        RelationshipConstraint::make('company')
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.company'))
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
                            ->label(__('inventories::filament/clusters/operations/resources/scrap.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ])->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Scrap $record): bool => $record->state == ScrapState::DONE)
                        ->action(function (Scrap $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/scrap.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/scrap.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => ScrapResource::can('delete', $record) && $record->state !== ScrapState::DONE,
            );
    }
}
