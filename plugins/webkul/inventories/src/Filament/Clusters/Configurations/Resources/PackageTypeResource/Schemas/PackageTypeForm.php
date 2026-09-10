<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.name'))
                            ->placeholder(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.name'))
                            ->prefixIcon('heroicon-o-tag')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('barcode')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.barcode'))
                                    ->prefixIcon('heroicon-o-bars-4')
                                    ->maxLength(255),

                                Select::make('company_id')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.company'))
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(current_company_id()),
                            ]),
                    ])->columnSpanFull(),

                Section::make(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.title'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('length')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.length'))
                                    ->prefixIcon('heroicon-o-arrows-right-left')
                                    ->suffix('cm')
                                    ->helperText(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.length').' (cm)')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),

                                TextInput::make('width')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.width'))
                                    ->prefixIcon('heroicon-o-arrows-up-down')
                                    ->suffix('cm')
                                    ->helperText(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.width').' (cm)')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),

                                TextInput::make('height')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.height'))
                                    ->prefixIcon('heroicon-o-arrow-trending-up')
                                    ->suffix('cm')
                                    ->helperText(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.height').' (cm)')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                            ]),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.weight'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('base_weight')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.weight'))
                                    ->prefixIcon('heroicon-o-scale')
                                    ->suffix('kg')
                                    ->helperText(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.weight').' (kg)')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),

                                TextInput::make('max_weight')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.max-weight'))
                                    ->prefixIcon('heroicon-o-scale')
                                    ->suffix('kg')
                                    ->helperText(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.max-weight').' (kg)')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                            ]),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
