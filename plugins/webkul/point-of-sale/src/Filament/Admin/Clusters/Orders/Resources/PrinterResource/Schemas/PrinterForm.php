<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\PointOfSale\Enums\PrinterType;

class PrinterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/orders/resources/printer.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        Select::make('printer_type')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.form.sections.general.fields.printer-type'))
                            ->options(PrinterType::class)
                            ->native(false)
                            ->required(),

                        TextInput::make('proxy_ip')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.form.sections.general.fields.proxy-ip'))
                            ->maxLength(64),

                        Select::make('company_id')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Select::make('categories')
                            ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.form.sections.general.fields.categories'))
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
