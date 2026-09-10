<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->unique(ignoreRecord: true),

                Select::make('users')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.form.users'))
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Select::make('company_id')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.form.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->default(current_company_id()),
            ])
            ->columns(1);
    }
}
