<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\ProjectStages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectStageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/project-stage.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ])
            ->columns(1);
    }
}
