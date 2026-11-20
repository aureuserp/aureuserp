<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/tag.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                ColorPicker::make('color')
                    ->label(__('crm::filament/clusters/configurations/resources/tag.form.fields.color'))
                    ->hexColor(),
            ])
            ->columns(1);
    }
}
