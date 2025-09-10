<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class CalendarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.title'))
                                    ->schema([
                                        Hidden::make('creator_id')
                                            ->default(Auth::user()->id),
                                        TextInput::make('name')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.schedule-name'))
                                            ->maxLength(255)
                                            ->required()
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.schedule-name-tooltip')),
                                        Select::make('timezone')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.timezone'))
                                            ->options(function () {
                                                return collect(timezone_identifiers_list())->mapWithKeys(function ($timezone) {
                                                    return [$timezone => $timezone];
                                                });
                                            })
                                            ->default(date_default_timezone_get())
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.timezone-tooltip')),
                                        Select::make('company_id')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ])->columns(2),
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.title'))
                                    ->schema([
                                        TextInput::make('hours_per_day')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.hours-per-day'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(24)
                                            ->default(8)
                                            ->suffix(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.hours-per-day-suffix')),
                                        TextInput::make('full_time_required_hours')
                                            ->label('Full-Time Required Hours')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.full-time-required-hours'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(168)
                                            ->default(40)
                                            ->suffix(__('employees::filament/clusters/configurations/resources/calendar.form.sections.configuration.fields.full-time-required-hours-suffix')),
                                    ])->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.title'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.status'))
                                            ->default(true)
                                            ->inline(false),
                                        Toggle::make('two_weeks_calendar')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.two-weeks-calendar'))
                                            ->inline(false)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'Enable alternating two-week work schedule'),
                                        Toggle::make('flexible_hours')
                                            ->label(__('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.flexible-hours'))
                                            ->inline(false)
                                            ->live()
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('employees::filament/clusters/configurations/resources/calendar.form.sections.flexibility.fields.flexible-hours-tooltip')),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
