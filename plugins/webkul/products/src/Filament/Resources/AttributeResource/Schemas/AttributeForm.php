<?php

namespace Webkul\Product\Filament\Resources\AttributeResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Product\Enums\AttributeType;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('products::filament/resources/attribute.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('products::filament/resources/attribute.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Radio::make('type')
                            ->label(__('products::filament/resources/attribute.form.sections.general.fields.type'))
                            ->required()
                            ->options(AttributeType::class)
                            ->default(AttributeType::RADIO->value)
                            ->live(),
                    ]),

                Section::make(__('products::filament/resources/attribute.form.sections.options.title'))
                    ->schema([
                        Repeater::make(__('products::filament/resources/attribute.form.sections.options.title'))
                            ->hiddenLabel()
                            ->compact()
                            ->relationship('options')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                ColorPicker::make('color')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.color'))
                                    ->hexColor()
                                    ->visible(fn (Get $get): bool => $get('../../type') === AttributeType::COLOR),
                                TextInput::make('extra_price')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.extra-price'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                            ])
                            ->table(fn ($livewire): array => array_values(array_filter([
                                TableColumn::make('name')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.name'))
                                    ->markAsRequired(),
                                in_array(data_get($livewire, 'data.type'), [AttributeType::COLOR, AttributeType::COLOR->value], strict: true)
                                    ? TableColumn::make('color')
                                        ->label(__('products::filament/resources/attribute.form.sections.options.fields.color'))
                                    : null,
                                TableColumn::make('extra_price')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.extra-price'))
                                    ->markAsRequired(),
                            ]))),
                    ]),
            ]);
    }
}
