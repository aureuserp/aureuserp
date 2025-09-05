<?php

namespace Webkul\Security\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('security::filament/resources/team.form.fields.name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
