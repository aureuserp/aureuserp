<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class BillOfMaterialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.entries.product'))
                                    ->size(TextSize::Large)
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                                TextEntry::make('product_variant')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.entries.product-variant'))
                                    ->state(fn (BillOfMaterial $record): string => $record->product?->parent_id ? $record->product->name : '—')
                                    ->columnSpanFull(),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('quantity')
                                            ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.entries.quantity'))
                                            ->numeric(decimalPlaces: 4),
                                        TextEntry::make('uom.name')
                                            ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.entries.uom'))
                                            ->placeholder('—'),
                                    ]),
                                TextEntry::make('code')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.entries.reference'))
                                    ->placeholder('—'),
                                TextEntry::make('type')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.entries.type'))
                                    ->badge(),
                                TextEntry::make('company.name')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.general.entries.company'))
                                    ->placeholder('—'),
                            ])
                            ->columns(2),
                        Tabs::make('bom-details')
                            ->tabs([
                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.components.title'))
                                    ->schema([
                                        RepeatableEntry::make('lines')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->table([
                                                InfolistTableColumn::make('product.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.components.entries.component'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('quantity')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.components.entries.quantity'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('uom.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.components.entries.uom'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('attribute_values')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.apply-on-variants'))
                                                    ->toggleable(isToggledHiddenByDefault: true)
                                                    ->resizable(),
                                                InfolistTableColumn::make('operation.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.consumed-in-operation'))
                                                    ->toggleable(isToggledHiddenByDefault: true)
                                                    ->resizable(),
                                                InfolistTableColumn::make('is_manual_consumption')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.highlight-consumption'))
                                                    ->toggleable(isToggledHiddenByDefault: true)
                                                    ->resizable(),
                                            ])
                                            ->schema([
                                                TextEntry::make('product.name')->placeholder('—'),
                                                TextEntry::make('quantity')->numeric(decimalPlaces: 4),
                                                TextEntry::make('uom.name')->placeholder('—'),
                                                TextEntry::make('attribute_values')
                                                    ->state(function ($record): string {
                                                        $labels = $record->attributeValues
                                                            ->map(fn ($value): string => $value->attribute?->name && $value->attributeOption?->name
                                                                ? "{$value->attribute->name}: {$value->attributeOption->name}"
                                                                : ($value->attributeOption?->name ?? (string) $value->id))
                                                            ->filter()
                                                            ->values();

                                                        return $labels->isNotEmpty() ? $labels->implode(', ') : '—';
                                                    }),
                                                TextEntry::make('operation.name')->placeholder('—'),
                                                IconEntry::make('is_manual_consumption')->boolean(),
                                            ]),
                                    ]),
                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.operations.title'))
                                    ->schema([
                                        RepeatableEntry::make('operations')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->table([
                                                InfolistTableColumn::make('name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.operation'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('workCenter.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.work-center'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('time_mode')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.time-mode'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('time_mode_batch')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.time-mode-batch'))
                                                    ->toggleable(isToggledHiddenByDefault: true)
                                                    ->resizable(),
                                                InfolistTableColumn::make('company.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.company'))
                                                    ->toggleable(isToggledHiddenByDefault: true)
                                                    ->resizable(),
                                                InfolistTableColumn::make('attribute_values')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.apply-on-variants'))
                                                    ->toggleable(isToggledHiddenByDefault: true)
                                                    ->resizable(),
                                                InfolistTableColumn::make('manual_cycle_time')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.duration'))
                                                    ->resizable(),
                                            ])
                                            ->schema([
                                                TextEntry::make('name')->placeholder('—'),
                                                TextEntry::make('workCenter.name')->placeholder('—'),
                                                TextEntry::make('time_mode')->badge(),
                                                TextEntry::make('time_mode_batch')->placeholder('—'),
                                                TextEntry::make('company.name')->placeholder('—'),
                                                TextEntry::make('attribute_values')
                                                    ->state(function ($record): string {
                                                        $labels = $record->attributeValues
                                                            ->map(fn ($value): string => $value->attribute?->name && $value->attributeOption?->name
                                                                ? "{$value->attribute->name}: {$value->attributeOption->name}"
                                                                : ($value->attributeOption?->name ?? (string) $value->id))
                                                            ->filter()
                                                            ->values();

                                                        return $labels->isNotEmpty() ? $labels->implode(', ') : '—';
                                                    }),
                                                TextEntry::make('manual_cycle_time')
                                                    ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 60, 'minutes')),
                                            ]),
                                    ]),
                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.by-products.title'))
                                    ->hidden(! BillsOfMaterialResource::getOperationSettings()->enable_byproducts)
                                    ->schema([
                                        RepeatableEntry::make('byproducts')
                                            ->hiddenLabel()
                                            ->contained(false)
                                            ->table([
                                                InfolistTableColumn::make('product.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.by-products.entries.product'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('quantity')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.by-products.entries.quantity'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('uom.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.by-products.entries.uom'))
                                                    ->resizable(),
                                                InfolistTableColumn::make('operation.name')
                                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.by-products.entries.operation'))
                                                    ->resizable(),
                                            ])
                                            ->schema([
                                                TextEntry::make('product.name')->placeholder('—'),
                                                TextEntry::make('quantity')->numeric(decimalPlaces: 4),
                                                TextEntry::make('uom.name')->placeholder('—'),
                                                TextEntry::make('operation.name')->placeholder('—'),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.title'))
                            ->schema([
                                TextEntry::make('kit_information')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.kit-information'))
                                    ->state(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.kit-information-content'))
                                    ->visible(fn (BillOfMaterial $record): bool => $record->type === BillOfMaterialType::PHANTOM)
                                    ->columnSpanFull(),
                                TextEntry::make('ready_to_produce')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.ready-to-produce'))
                                    ->badge()
                                    ->visible(fn (BillOfMaterial $record): bool => $record->type === BillOfMaterialType::NORMAL)
                                    ->columnSpanFull(),
                                TextEntry::make('operationType.name')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.routing'))
                                    ->placeholder('—')
                                    ->visible(fn (BillOfMaterial $record): bool => $record->type === BillOfMaterialType::NORMAL)
                                    ->columnSpanFull(),
                                TextEntry::make('consumption')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.consumption'))
                                    ->badge()
                                    ->visible(fn (BillOfMaterial $record): bool => $record->type === BillOfMaterialType::NORMAL)
                                    ->columnSpanFull(),
                                IconEntry::make('allow_operation_dependencies')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.operation-dependencies'))
                                    ->boolean()
                                    ->columnSpanFull(),
                                TextEntry::make('produce_delay')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.manufacturing-lead-time'))
                                    ->suffix(' '.__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.days-suffix'))
                                    ->columnSpanFull(),
                                TextEntry::make('days_to_prepare_mo')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.days-to-prepare-manufacturing-order'))
                                    ->suffix(' '.__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.tabs.miscellaneous.entries.days-suffix'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        Section::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.record-information.entries.created-by'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('created_at')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.record-information.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('updated_at')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.infolist.sections.record-information.entries.last-updated'))
                                    ->dateTime()
                                    ->icon('heroicon-m-clock'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
