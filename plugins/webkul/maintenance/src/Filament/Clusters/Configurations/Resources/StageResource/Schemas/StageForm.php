<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->unique(ignoreRecord: true),

                Toggle::make('done')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.form.fields.done')),
            ])
            ->columns(1);
    }
}
