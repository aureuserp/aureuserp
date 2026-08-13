<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Models\Order;

class ManufacturingOrdersTable
{
    public static function configure(Table $table, array $customColumns = []): Table
    {
        return $table
            ->reorderableColumns()
            ->defaultSort('id', 'desc')
            ->columns(array_merge([
                TextColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.reference'))
                    ->searchable(),
                TextColumn::make('started_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.start'))
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('finished_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.end'))
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deadline_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.deadline'))
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.product'))
                    ->searchable(),
                TextColumn::make('producingLot.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.lot-serial-number'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bill_of_material_id')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.bill-of-material'))
                    ->formatStateUsing(fn (mixed $state, Order $record): string => ManufacturingOrderResource::getBillOfMaterialLabel($record->billOfMaterial))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('origin')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.source'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('assignedUser.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.responsible'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reservation_state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.mo-readiness'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('components_availability')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.component-status'))
                    ->badge()
                    ->state(fn (Order $record): ?string => $record->components_availability)
                    ->color(fn (Order $record): string => match ($record->components_availability_state) {
                        'available'   => 'success',
                        'unavailable' => 'danger',
                        'late'        => 'warning',
                        'expected'    => 'info',
                        default       => 'gray',
                    })
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.quantity'))
                    ->numeric(decimalPlaces: 4),
                TextColumn::make('uom.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.uom'))
                    ->placeholder('—'),
                TextColumn::make('consumption_efficiency')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.consumption-efficiency'))
                    ->state(fn (Order $record): string => $record->consumption_efficiency !== null ? $record->consumption_efficiency.'%' : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('work_orders_expected_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.expected-duration'))
                    ->state(fn (Order $record): string => format_float_time((float) ($record->workOrders->sum('expected_duration') ?: 0), 'minutes'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('work_orders_real_duration')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.real-duration'))
                    ->state(fn (Order $record): string => format_float_time((float) ($record->workOrders->sum('duration') ?: 0), 'minutes'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.company'))
                    ->placeholder('—'),
                TextColumn::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.columns.state'))
                    ->badge(),
            ], $customColumns))
            ->groups([
                TableGroup::make('state')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.state')),
                TableGroup::make('product.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.product')),
                TableGroup::make('billOfMaterial.reference')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.bill-of-material')),
                TableGroup::make('assignedUser.name')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.responsible')),
                TableGroup::make('deadline_at')
                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.table.groups.deadline'))
                    ->date(),
            ])
            ->recordTitleAttribute('name')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
