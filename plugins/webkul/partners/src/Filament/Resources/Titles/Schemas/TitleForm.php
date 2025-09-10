<?php

namespace Webkul\Partner\Filament\Resources\Titles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TitleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('partners::filament/resources/title.form.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_name')
                    ->label(__('partners::filament/resources/title.form.short-name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
