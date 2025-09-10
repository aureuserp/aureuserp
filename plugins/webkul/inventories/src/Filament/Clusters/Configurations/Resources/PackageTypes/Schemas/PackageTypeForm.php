<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
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
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),

                        Fieldset::make(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.title'))
                            ->schema([
                                TextInput::make('length')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.length'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                                TextInput::make('width')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.width'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                                TextInput::make('height')
                                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.fieldsets.size.fields.height'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                            ])
                            ->columns(3),
                        TextInput::make('base_weight')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.weight'))
                            ->required()
                            ->numeric()
                            ->default(0.0000)
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('max_weight')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.max-weight'))
                            ->required()
                            ->numeric()
                            ->default(0.0000)
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('barcode')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.barcode'))
                            ->maxLength(255),
                        Select::make('company_id')
                            ->label(__('inventories::filament/clusters/configurations/resources/package-type.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columnSpanFull(),
            ]);
    }
}
