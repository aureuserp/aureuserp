<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\NoteResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/note.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/note.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        ColorPicker::make('color')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/note.form.sections.general.fields.color'))
                            ->hexColor(),
                    ])
                    ->columns(2),
            ]);
    }
}
