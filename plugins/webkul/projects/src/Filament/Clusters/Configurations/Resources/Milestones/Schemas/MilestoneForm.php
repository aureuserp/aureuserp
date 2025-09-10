<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\Milestones\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Webkul\Project\Filament\Resources\Projects\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\Projects\RelationManagers\MilestonesRelationManager;

class MilestoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.form.name'))
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('deadline')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.form.deadline'))
                    ->native(false),
                Toggle::make('is_completed')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.form.is-completed'))
                    ->required(),
                Select::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.form.project'))
                    ->relationship('project', 'name')
                    ->hiddenOn([
                        MilestonesRelationManager::class,
                        ManageMilestones::class,
                    ])
                    ->required()
                    ->searchable()
                    ->preload(),
            ])
            ->columns(1);
    }
}
