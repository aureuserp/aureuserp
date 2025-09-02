<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource\Pages;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\TaskStagesRelationManager;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\TaskStage;

class TaskStageResource extends Resource
{
    protected static ?string $model = TaskStage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/task-stage.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.form.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.form.project'))
                    ->relationship(
                        'project',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->required()
                    ->searchable()
                    ->preload(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.columns.project'))
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.filters.project'))
                    ->relationship('project', 'name')
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Tables\Grouping\Group::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.groups.project')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.restore.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTaskStages::route('/'),
        ];
    }
}
