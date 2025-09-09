<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PublicHolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Group::make()
                        ->schema([
                            Hidden::make('time_type')
                                ->default('leave'),
                            TextInput::make('name')
                                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.name'))
                                ->required()
                                ->placeholder(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.name-placeholder')),
                        ])->columns(2),

                    Group::make()
                        ->schema([
                            DatePicker::make('date_from')
                                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.date-from'))
                                ->native(false)
                                ->required(),
                            DatePicker::make('date_to')
                                ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.date-to'))
                                ->required()
                                ->native(false)
                                ->minDate(function (callable $get) {
                                    return $get('date_from') ?: now()->toDateString();
                                })
                                ->live()
                                ->rules([
                                    'required',
                                    'date',
                                    'after_or_equal:date_from',
                                ]),
                        ])->columns(2),
                    Select::make('calendar')
                        ->searchable()
                        ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.form.fields.calendar'))
                        ->preload()
                        ->relationship('calendar', 'name'),
                ])->columnSpanFull(),
            ]);
    }
}
