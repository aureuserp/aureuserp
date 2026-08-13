<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Schemas;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Currency;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/currency.form.sections.currency-details.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('support::filament/resources/currency.form.sections.currency-details.fields.name-tooltip')),
                                        TextInput::make('symbol')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.symbol'))
                                            ->maxLength(10),
                                        TextInput::make('full_name')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.full-name'))
                                            ->maxLength(255),
                                        TextInput::make('iso_numeric')
                                            ->label(__('support::filament/resources/currency.form.sections.currency-details.fields.iso-numeric'))
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(999),
                                    ])
                                    ->columns(2),
                                Section::make(__('support::filament/resources/currency.form.sections.format-information.title'))
                                    ->schema([
                                        TextInput::make('decimal_places')
                                            ->label(__('support::filament/resources/currency.form.sections.format-information.fields.decimal-places'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(6)
                                            ->default(2),
                                        TextInput::make('rounding')
                                            ->label(__('support::filament/resources/currency.form.sections.format-information.fields.rounding'))
                                            ->numeric()
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->default(0.00)
                                            ->helperText(__('support::filament/resources/currency.form.sections.format-information.fields.rounding-helper-text')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/currency.form.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        Toggle::make('active')
                                            ->label(__('support::filament/resources/currency.form.sections.status-and-configuration-information.fields.status'))
                                            ->default(true)
                                            ->rule(static fn (?Currency $record): Closure => static function (string $attribute, $value, Closure $fail) use ($record): void {
                                                if ($record && ! $value && $record->isInUse()) {
                                                    $fail(__('support::filament/resources/currency.table.actions.deactivate.notification.body'));
                                                }
                                            }),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
                Section::make(__('support::filament/resources/currency.form.sections.rates.title'))
                    ->description(__('support::filament/resources/currency.form.sections.rates.description'))
                    ->schema([
                        Repeater::make('rates')
                            ->relationship('rates')
                            ->hiddenLabel()
                            ->compact()
                            ->minItems(1)
                            ->addActionLabel(__('support::filament/resources/currency.form.sections.rates.add-rate'))
                            ->deleteAction(function (Action $action) {
                                return $action->requiresConfirmation();
                            })
                            ->cloneable()
                            ->table([
                                TableColumn::make('name')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.name'))
                                    ->resizable(),
                                TableColumn::make('rate')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.unit-per-currency', [
                                        'currency' => config('app.currency'),
                                    ]))
                                    ->resizable(),
                                TableColumn::make('rate_temp')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.currency-per-unit', [
                                        'currency' => config('app.currency'),
                                    ]))
                                    ->resizable(),
                            ])
                            ->schema([
                                DatePicker::make('name')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.name'))
                                    ->required()
                                    ->native(false)
                                    ->default(today())
                                    ->format('Y-m-d')
                                    ->displayFormat('Y-m-d'),
                                TextInput::make('rate')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.unit-per-currency', [
                                        'currency' => config('app.currency'),
                                    ]))
                                    ->required()
                                    ->numeric()
                                    ->step(0.000001)
                                    ->minValue(1)
                                    ->default(1.000000)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state && $state > 0) {
                                            $set('rate_temp', round(1 / $state, 6));
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, callable $set) {
                                        if ($state && $state > 0) {
                                            $set('rate_temp', round(1 / $state, 6));
                                        }
                                    }),
                                TextInput::make('rate_temp')
                                    ->label(__('support::filament/resources/currency.form.sections.rates.fields.currency-per-unit', [
                                        'currency' => config('app.currency'),
                                    ]))
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->default(1.000000),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
