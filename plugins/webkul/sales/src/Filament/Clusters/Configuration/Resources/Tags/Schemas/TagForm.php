<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\Tags\Schemas;

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
                    ->label(__('sales::filament/clusters/configurations/resources/tag.form.fields.name'))
                    ->required()
                    ->placeholder(__('Name')),
                ColorPicker::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.form.fields.color'))
                    ->hexColor(),
            ]);
    }
}
