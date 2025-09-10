<?php

namespace Webkul\Account\Filament\Resources\IncoTerms\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class IncotermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('creator_id')
                    ->default(Auth::id())
                    ->required(),
                TextInput::make('code')
                    ->label(__('accounts::filament/resources/incoterm.form.fields.code'))
                    ->maxLength(3)
                    ->required(),
                TextInput::make('name')
                    ->label(__('accounts::filament/resources/incoterm.form.fields.name'))
                    ->required(),
            ]);
    }
}
