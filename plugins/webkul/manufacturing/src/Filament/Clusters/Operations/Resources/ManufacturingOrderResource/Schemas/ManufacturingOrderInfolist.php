<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class ManufacturingOrderInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function (Order $record): array {
                        $options = ManufacturingOrderState::options();

                        unset(
                            $options[ManufacturingOrderState::PROGRESS->value],
                            $options[ManufacturingOrderState::TO_CLOSE->value],
                            $options[ManufacturingOrderState::CANCEL->value],
                        );

                        if ($record->state === ManufacturingOrderState::CANCEL) {
                            $options[ManufacturingOrderState::CANCEL->value] = ManufacturingOrderState::CANCEL->getLabel();
                        }

                        return $options;
                    }),

                Section::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.title'))
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.product'))
                                    ->size(TextSize::Large)
                                    ->placeholder('—'),
                                TextEntry::make('quantity')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.quantity'))
                                    ->state(function (Order $record): string {
                                        $expectedQuantity = number_format((float) $record->quantity, 4, '.', '');

                                        if ($record->state === ManufacturingOrderState::DRAFT) {
                                            return $expectedQuantity.' '.($record->uom?->name ?? '—');
                                        }

                                        $producingQuantity = number_format((float) ($record->quantity_producing ?: 0), 4, '.', '');

                                        return $producingQuantity.' / '.$expectedQuantity.' '.($record->uom?->name ?? '—');
                                    }),
                                TextEntry::make('bill_of_material_id')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.bill-of-material'))
                                    ->state(fn (Order $record): string => ManufacturingOrderResource::getBillOfMaterialLabel($record->billOfMaterial)),
                                TextEntry::make('consumption_efficiency')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.consumption-efficiency'))
                                    ->state(fn (Order $record): string => $record->consumption_efficiency !== null ? $record->consumption_efficiency.'%' : '—'),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('started_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.scheduled-date'))
                                    ->dateTime(),
                                TextEntry::make('finished_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.sections.general.fields.scheduled-end'))
                                    ->dateTime()
                                    ->placeholder('—')
                                    ->visible(fn (Order $record): bool => $record->state !== ManufacturingOrderState::DRAFT),
                                TextEntry::make('assignedUser.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.sections.general.entries.responsible'))
                                    ->placeholder('—'),
                            ]),
                    ]),
                Tabs::make('manufacturing-order-details')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.components.title'))
                            ->schema([
                                RepeatableEntry::make('rawMaterialMoves')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.component')),
                                        InfolistTableColumn::make('sourceLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.from'))
                                            ->visible(ManufacturingOrderResource::getWarehouseSettings()->enable_locations),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.to-consume')),
                                        InfolistTableColumn::make('quantity')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.quantity'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        InfolistTableColumn::make('uom.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.components.columns.uom')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state, Move $record): string => $record->product?->trashed() ? $state.' (Deleted)' : $state)
                                            ->placeholder('—'),
                                        TextEntry::make('sourceLocation.full_name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product_uom_qty')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4),
                                        TextEntry::make('quantity')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4)
                                            ->placeholder('—'),
                                        TextEntry::make('uom.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.work-orders.title'))
                            ->schema([
                                RepeatableEntry::make('workOrders')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('operation.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.operation')),
                                        InfolistTableColumn::make('workCenter.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.work-center')),
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.product')),
                                        InfolistTableColumn::make('quantity_remaining')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.quantity-remaining')),
                                        InfolistTableColumn::make('started_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.start'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        InfolistTableColumn::make('finished_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.end'))
                                            ->toggleable(isToggledHiddenByDefault: true),
                                        InfolistTableColumn::make('expected_duration')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.expected-duration')),
                                        InfolistTableColumn::make('duration')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.real-duration')),
                                        InfolistTableColumn::make('state')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.work-orders.columns.status')),
                                    ])
                                    ->schema([
                                        TextEntry::make('operation.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('workCenter.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('quantity_remaining')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4)
                                            ->placeholder('—'),
                                        TextEntry::make('started_at')
                                            ->hiddenLabel()
                                            ->dateTime()
                                            ->placeholder('—'),
                                        TextEntry::make('finished_at')
                                            ->hiddenLabel()
                                            ->dateTime()
                                            ->placeholder('—'),
                                        TextEntry::make('expected_duration')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                        TextEntry::make('duration')
                                            ->hiddenLabel()
                                            ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                        TextEntry::make('state')
                                            ->hiddenLabel()
                                            ->badge(),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.by-products.title'))
                            ->hidden(! ManufacturingOrderResource::getOperationSettings()->enable_byproducts)
                            ->schema([
                                RepeatableEntry::make('moveByproducts')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.product')),
                                        InfolistTableColumn::make('destinationLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.to')),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.to-produce')),
                                        InfolistTableColumn::make('uom.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.form.tabs.by-products.columns.uom')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('destinationLocation.full_name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                        TextEntry::make('product_uom_qty')
                                            ->hiddenLabel()
                                            ->numeric(decimalPlaces: 4),
                                        TextEntry::make('uom.name')
                                            ->hiddenLabel()
                                            ->placeholder('—'),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.title'))
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('operationType.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.operation-type'))
                                            ->formatStateUsing(fn (mixed $state, Order $record): string => ManufacturingOrderResource::getOperationTypeLabel($record->operationType)),
                                        TextEntry::make('sourceLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.source'))
                                            ->placeholder('—')
                                            ->visible(ManufacturingOrderResource::getWarehouseSettings()->enable_locations),
                                        TextEntry::make('finalLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.finished-products-location'))
                                            ->placeholder('—')
                                            ->visible(ManufacturingOrderResource::getWarehouseSettings()->enable_locations),
                                        TextEntry::make('company.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/manufacturing-order.infolist.tabs.miscellaneous.entries.company'))
                                            ->placeholder('—'),
                                        ...$customInfolistEntries,
                                    ]),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
