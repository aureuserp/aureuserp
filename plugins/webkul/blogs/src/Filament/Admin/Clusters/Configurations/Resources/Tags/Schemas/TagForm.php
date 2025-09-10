<?php

namespace Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Tags\Schemas;

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
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/tag.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                ColorPicker::make('color')
                    ->label(__('blogs::filament/admin/clusters/configurations/resources/tag.form.color'))
                    ->hexColor(),
            ]);
    }
}
