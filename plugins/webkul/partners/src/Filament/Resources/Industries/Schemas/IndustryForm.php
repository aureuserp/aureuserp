<?php

namespace Webkul\Partner\Filament\Resources\Industries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndustryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('partners::filament/resources/industry.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('description')
                    ->label(__('partners::filament/resources/industry.form.full-name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
