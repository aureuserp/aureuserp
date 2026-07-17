<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Product\Enums\ProductType;

class InventoryProductSchema
{
    public static function formSection(): Section
    {
        return Section::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.title'))
            ->schema([
                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.title'))
                    ->schema([
                        Toggle::make('is_storable')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.fields.track-inventory'))
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                if (! $get('is_storable')) {
                                    $set('tracking', ProductTracking::QTY->value);

                                    $set('use_expiration_date', false);
                                }
                            }),
                        Select::make('tracking')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.fields.track-by'))
                            ->selectablePlaceholder(false)
                            ->options(ProductTracking::class)
                            ->default(ProductTracking::QTY->value)
                            ->visible(fn (Get $get, TraceabilitySettings $settings): bool => $settings->enable_lots_serial_numbers && (bool) $get('is_storable'))
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                if ($get('tracking') == ProductTracking::QTY->value) {
                                    $set('use_expiration_date', false);
                                }
                            }),
                    ])
                    ->columns(1),
                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.title'))
                    ->schema([
                        CheckboxList::make('routes')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.fields.routes'))
                            ->relationship(
                                'routes',
                                'name',
                                fn ($query) => $query->where('product_selectable', true)
                            )
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.fields.routes-hint-tooltip')),
                    ]),

                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Select::make('responsible_id')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.responsible'))
                            ->relationship('responsible', 'name')
                            ->searchable()
                            ->preload()
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.responsible-hint-tooltip')),
                        TextInput::make('weight')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.weight'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('volume')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.volume'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('sale_delay')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.sale-delay'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.sale-delay-hint-tooltip')),
                    ]),

                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.title'))
                    ->schema([
                        TextInput::make('expiration_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.expiration-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.expiration-date-hint-tooltip')),
                        TextInput::make('use_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.best-before-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.best-before-date-hint-tooltip')),
                        TextInput::make('removal_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.removal-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.removal-date-hint-tooltip')),
                        TextInput::make('alert_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.alert-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.alert-date-hint-tooltip')),
                    ])
                    ->visible(fn (Get $get): bool => (bool) $get('use_expiration_date')),
            ])
            ->visible(fn (Get $get): bool => $get('type') == ProductType::GOODS);
    }

    public static function infolistSection(): Section
    {
        return Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.title'))
            ->schema([
                Grid::make(3)
                    ->schema([
                        IconEntry::make('is_storable')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.track-inventory'))
                            ->boolean(),

                        TextEntry::make('tracking')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.track-by')),

                        IconEntry::make('use_expiration_date')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.expiration-date'))
                            ->boolean(),
                    ]),

                Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.operation.title'))
                    ->schema([
                        TextEntry::make('routes.name')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.operation.entries.routes'))
                            ->icon('heroicon-o-arrow-path')
                            ->listWithLineBreaks()
                            ->placeholder('—'),
                    ]),

                Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('responsible.name')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.responsible'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('weight')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.weight'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-scale'),

                                TextEntry::make('volume')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.volume'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-beaker'),

                                TextEntry::make('sale_delay')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.sale-delay'))
                                    ->placeholder('—'),
                            ]),
                    ]),

                Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.title'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('expiration_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.expiration-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('use_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.best-before-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('removal_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.removal-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('alert_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.alert-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->visible(fn ($record): bool => (bool) $record->use_expiration_date),
            ])
            ->visible(fn ($record): bool => $record->type == ProductType::GOODS);
    }
}
