<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Webkul\Employee\Enums\WorkLocation as WorkLocationEnum;

class WorkLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.name'))
                    ->required()
                    ->maxLength(255),
                ToggleButtons::make('location_type')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.location-type'))
                    ->inline()
                    ->options(WorkLocationEnum::class)
                    ->required(),
                TextInput::make('location_number')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.location-number')),
                Select::make('company_id')
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.company'))
                    ->required()
                    ->preload()
                    ->default(current_company_id())
                    ->relationship('company', 'name'),
                Toggle::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.status'))
                    ->required(),
            ]);
    }
}
