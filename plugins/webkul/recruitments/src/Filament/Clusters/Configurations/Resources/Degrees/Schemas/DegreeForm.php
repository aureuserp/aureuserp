<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Degrees\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DegreeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/degree.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('recruitments::filament/clusters/configurations/resources/degree.form.fields.name-placeholder')),
            ]);
    }
}
