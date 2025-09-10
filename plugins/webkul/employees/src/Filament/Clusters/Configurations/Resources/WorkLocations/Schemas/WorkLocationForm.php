<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocations\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Enums\WorkLocation as WorkLocationEnum;

class WorkLocationForm
{
    public static function configure(Schema $schema)
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.name'))
                    ->required()
                    ->maxLength(255),
                Hidden::make('creator_id')
                    ->required()
                    ->default(Auth::user()->id),
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
                    ->relationship('company', 'name'),
                Toggle::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.status'))
                    ->required(),
            ]);
    }
}
