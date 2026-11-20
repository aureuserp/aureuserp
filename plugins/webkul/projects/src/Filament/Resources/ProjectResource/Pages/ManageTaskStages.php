<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Livewire\Livewire;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource;
use Webkul\Project\Filament\Resources\ProjectResource;

class ManageTaskStages extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'taskStages';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getNavigationLabel(): string
    {
        return __('Task Stages');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return (string) Livewire::current()->getRecord()->taskStages()->count();
    }

    public function form(Schema $schema): Schema
    {
        return TaskStageResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return TaskStageResource::table($table)
            ->filters([])
            ->groups([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('projects::filament/resources/project/relation-managers/task-stages.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/project/relation-managers/task-stages.table.header-actions.create.notification.title'))
                            ->body(__('projects::filament/resources/project/relation-managers/task-stages.table.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
