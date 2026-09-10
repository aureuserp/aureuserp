<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlanResource\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Webkul\TimeOff\Enums\AccruedGainTime;
use Webkul\TimeOff\Enums\CarryoverDate;
use Webkul\TimeOff\Enums\CarryoverMonth;

class AccrualPlanForm
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
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.name'))
                                    ->required(),
                                Toggle::make('is_based_on_worked_time')
                                    ->inline(false)
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.is-based-on-worked-time')),
                                Radio::make('accrued_gain_time')
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.accrued-gain-time'))
                                    ->options(AccruedGainTime::class)
                                    ->default(AccruedGainTime::END->value)
                                    ->required(),
                                Radio::make('carryover_date')
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.carry-over-time'))
                                    ->options(CarryoverDate::class)
                                    ->default(CarryoverDate::OTHER->value)
                                    ->live()
                                    ->required(),
                                Fieldset::make()
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.carry-over-date'))
                                    ->live()
                                    ->visible(function (Get $get) {
                                        return $get('carryover_date') === CarryoverDate::OTHER->value;
                                    })
                                    ->schema([
                                        Select::make('carryover_day')
                                            ->hiddenLabel()
                                            ->options(function (Get $get) {
                                                $monthValue = $get('carryover_month') ?? CarryoverMonth::JAN->value;

                                                $monthEnum = CarryoverMonth::from($monthValue);

                                                $monthNumber = $monthEnum->toNumber();
                                                $year = now()->year;

                                                $daysInMonth = Carbon::createFromDate($year, $monthNumber, 1)->daysInMonth;

                                                return collect(range(1, $daysInMonth))
                                                    ->mapWithKeys(fn ($day) => [$day => $day])
                                                    ->toArray();
                                            })

                                            ->maxWidth(Width::ExtraSmall)
                                            ->default(1)
                                            ->required(),
                                        Select::make('carryover_month')
                                            ->hiddenLabel()
                                            ->options(CarryoverMonth::class)
                                            ->reactive()
                                            ->default(CarryoverMonth::JAN->value)
                                            ->required(),
                                    ])->columns(2),
                                Toggle::make('is_active')
                                    ->inline(false)
                                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.form.fields.status')),
                            ]),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }
}
