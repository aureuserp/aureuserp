<?php

namespace Webkul\Account\Filament\Resources\PaymentTerms\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\EarlyPayDiscount;

class PaymentTermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.payment-term'))
                                    ->maxLength(255)
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpan(1),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                Toggle::make('early_discount')
                                    ->live()
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.early-discount')),
                            ])->columns(2),
                        Group::make()
                            ->visible(fn (Get $get) => $get('early_discount'))
                            ->schema([
                                TextInput::make('discount_percentage')
                                    ->required()
                                    ->numeric()
                                    ->maxValue(100)
                                    ->minValue(0)
                                    ->suffix(__('%'))
                                    ->hiddenLabel(),
                                TextInput::make('discount_days')
                                    ->required()
                                    ->integer()
                                    ->minValue(0)
                                    ->prefix(__('accounts::filament/resources/payment-term.form.sections.fields.discount-days-prefix'))
                                    ->suffix(__('accounts::filament/resources/payment-term.form.sections.fields.discount-days-suffix'))
                                    ->hiddenLabel(),
                            ])->columns(4),
                        Group::make()
                            ->visible(fn (Get $get) => $get('early_discount'))
                            ->schema([
                                Select::make('early_pay_discount')
                                    ->label(__('accounts::filament/resources/payment-term.form.sections.fields.reduced-tax'))
                                    ->options(EarlyPayDiscount::class)
                                    ->default(EarlyPayDiscount::INCLUDED->value),
                            ])->columns(2),
                        RichEditor::make('note')
                            ->label(__('accounts::filament/resources/payment-term.form.sections.fields.note')),
                    ]),
            ]);
    }
}
