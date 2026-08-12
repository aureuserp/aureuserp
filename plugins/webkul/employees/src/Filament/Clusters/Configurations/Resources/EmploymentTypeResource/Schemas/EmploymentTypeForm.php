<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmploymentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),
                TextInput::make('code')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.form.fields.code')),
                Select::make('country_id')
                    ->searchable()
                    ->preload()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.form.fields.country'))
                    ->relationship('country', 'name'),
            ])
            ->columns(2);
    }
}
