<?php

namespace Webkul\Product\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Product\Enums\AttributeType;

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
                            ->relationship('options')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                ColorPicker::make('color')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.color'))
                                    ->hexColor()
                                    ->visible(fn (Get $get): bool => $get('../../type') === AttributeType::COLOR->value),
                                TextInput::make('extra_price')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.extra-price'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
