<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource;
use Webkul\Project\Filament\Resources\ProjectResource;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ProjectResource::getTaskSettings()->enable_milestones && $ownerRecord->allow_milestones;
    }

    public function form(Schema $schema): Schema
    {
        return MilestoneResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return MilestoneResource::table($table)
            ->filters([])
            ->groups([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('projects::filament/resources/project/relation-managers/milestones.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/project/relation-managers/milestones.table.header-actions.create.notification.title'))
                            ->body(__('projects::filament/resources/project/relation-managers/milestones.table.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
