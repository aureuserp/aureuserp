<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Webkul\Project\Filament\Resources\Projects\RelationManagers\TaskStagesRelationManager;

class TaskStageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.form.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.form.project'))
                    ->relationship('project', 'name')
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->required()
                    ->searchable()
                    ->preload(),
            ])
            ->columns(1);
    }
}
