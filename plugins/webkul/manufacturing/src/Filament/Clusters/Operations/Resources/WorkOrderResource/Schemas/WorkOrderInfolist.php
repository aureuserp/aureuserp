<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource;
use Webkul\Manufacturing\Models\WorkOrder;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class WorkOrderInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(fn (?WorkOrder $record): array => WorkOrderResource::getVisibleWorkOrderStateOptions($record?->state?->value ?? $record?->state))
                    ->disabled(),

                Section::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.title'))
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('operation.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.work-order'))
                                    ->placeholder('—'),
                                TextEntry::make('workCenter.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.work-center'))
                                    ->placeholder('—'),
                                TextEntry::make('product.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.product'))
                                    ->placeholder('—'),
                                TextEntry::make('quantity_remaining')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.quantity'))
                                    ->formatStateUsing(fn (mixed $state, WorkOrder $record): string => number_format((float) ($state ?: 0), 4).' '.($record->uom?->name ?? '—')),
                                TextEntry::make('manufacturingOrder.reference')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.manufacturing-order'))
                                    ->formatStateUsing(fn (mixed $state, WorkOrder $record): string => WorkOrderResource::getManufacturingOrderLabel($record->manufacturingOrder)),
                                TextEntry::make('manufacturingOrder.producingLot.name')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.lot-serial'))
                                    ->placeholder('—'),
                            ]),
                        Group::make()
                            ->columns(1)
                            ->schema([
                                TextEntry::make('started_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.start-date'))
                                    ->dateTime()
                                    ->placeholder('—'),
                                TextEntry::make('finished_at')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.end-date'))
                                    ->dateTime()
                                    ->placeholder('—'),
                                TextEntry::make('expected_duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.expected-duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                TextEntry::make('duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.sections.general.entries.real-duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                            ]),
                    ]),

                Tabs::make('work-order-infolist-tabs')
                    ->tabs([
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.time-tracking.title'))
                            ->schema([
                                RepeatableEntry::make('productivityLogs')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('assignedUser.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.user')),
                                        InfolistTableColumn::make('duration')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.duration')),
                                        InfolistTableColumn::make('started_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.start-date')),
                                        InfolistTableColumn::make('finished_at')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.end-date')),
                                        InfolistTableColumn::make('loss.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.time-tracking.columns.productivity')),
                                    ])
                                    ->schema([
                                        TextEntry::make('assignedUser.name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('duration')->hiddenLabel()->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes')),
                                        TextEntry::make('started_at')->hiddenLabel()->dateTime()->placeholder('—'),
                                        TextEntry::make('finished_at')->hiddenLabel()->dateTime()->placeholder('—'),
                                        TextEntry::make('loss.name')->hiddenLabel()->placeholder('—'),
                                    ]),
                                TextEntry::make('duration')
                                    ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.time-tracking.footer.real-duration'))
                                    ->formatStateUsing(fn (mixed $state): string => format_float_time((float) ($state ?: 0), 'minutes').' (minutes)'),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.components.title'))
                            ->schema([
                                RepeatableEntry::make('rawMaterialMoves')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.product')),
                                        InfolistTableColumn::make('sourceLocation.full_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.from')),
                                        InfolistTableColumn::make('product_uom_qty')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.to-consume')),
                                        InfolistTableColumn::make('uom.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.form.tabs.components.columns.uom')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('sourceLocation.full_name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('product_uom_qty')->hiddenLabel()->numeric(decimalPlaces: 4),
                                        TextEntry::make('uom.name')->hiddenLabel()->placeholder('—'),
                                    ]),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.work-instruction.title'))
                            ->schema([
                                ViewEntry::make('work_instruction_preview')
                                    ->hiddenLabel()
                                    ->view('manufacturing::filament.clusters.operations.resources.work-order.work-instruction-preview'),
                            ]),
                        Tab::make(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.title'))
                            ->schema([
                                RepeatableEntry::make('blockedByWorkOrders')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->table([
                                        InfolistTableColumn::make('display_name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.columns.work-order')),
                                        InfolistTableColumn::make('workCenter.name')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.columns.work-center')),
                                        InfolistTableColumn::make('state')
                                            ->label(__('manufacturing::filament/clusters/operations/resources/work-order.infolist.tabs.blocked-by.columns.status')),
                                    ])
                                    ->schema([
                                        TextEntry::make('display_name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('workCenter.name')->hiddenLabel()->placeholder('—'),
                                        TextEntry::make('state')->hiddenLabel()->badge(),
                                    ]),
                            ]),
                    ]),

                Section::make()
                    ->schema($customInfolistEntries)
                    ->columns(2),
            ])
            ->columns(1);
    }
}
