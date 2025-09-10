<?php

namespace Webkul\Account\Filament\Resources\CashRounding\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;

class CashRoundingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.name'))
                                    ->autofocus(),
                                TextInput::make('rounding')
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.rounding-precision'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.01)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                                Select::make('strategy')
                                    ->options(RoundingStrategy::class)
                                    ->default(RoundingStrategy::BIGGEST_TAX->value)
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.rounding-strategy')),
                                Select::make('rounding_method')
                                    ->options(RoundingMethod::class)
                                    ->default(RoundingMethod::HALF_UP->value)
                                    ->label(__('accounts::filament/resources/cash-rounding.form.fields.rounding-method'))
                                    ->required()
                                    ->autofocus(),
                            ]),
                    ])->columns(2),
            ]);
    }
}
