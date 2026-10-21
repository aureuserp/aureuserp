<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class BillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(64)
                            ->autofocus(),

                        TextInput::make('value')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.form.sections.general.fields.value'))
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        Toggle::make('is_for_all_configs')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.form.sections.general.fields.is-for-all-configs'))
                            ->helperText(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.form.sections.general.fields.is-for-all-configs-helper-text'))
                            ->default(true)
                            ->live(),

                        Select::make('configs')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.form.sections.general.fields.configs'))
                            ->relationship('configs', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->disabled(fn (Get $get): bool => (bool) $get('is_for_all_configs'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
