<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\TaskStagesRelationManager;

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
                    ->relationship(
                        'project',
                        'name',
                        modifyQueryUsing: fn (Builder $query, $state) => $query->withTrashed()
                            ->where(hide_deleted_unless_selected($state)),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.trashed_label_suffix($record);
                    })
                    ->disableOptionWhen(fn ($component, $value): bool => is_trashed_option($component, $value))
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->required()
                    ->searchable()
                    ->preload(),
            ])
            ->columns(1);
    }
}
