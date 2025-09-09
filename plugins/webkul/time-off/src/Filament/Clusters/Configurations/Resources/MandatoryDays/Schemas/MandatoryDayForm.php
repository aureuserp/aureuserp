<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MandatoryDayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ColorPicker::make('color')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.color'))
                    ->required()
                    ->hexColor()
                    ->default('#000000'),
                TextInput::make('name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.name'))
                    ->required(),
                DatePicker::make('start_date')
                    ->native(false)
                    ->default(now()->format('Y-m-d'))
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.start-date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->native(false)
                    ->default(now()->format('Y-m-d'))
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.form.fields.end-date'))
                    ->required(),
            ]);
    }
}
