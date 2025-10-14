<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource\Pages\ManageTaskStages;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\TaskStagesRelationManager;
use Webkul\Project\Models\TaskStage;

class TaskStageResource extends Resource
{
    protected static ?string $model = TaskStage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/task-stage.navigation.title');
    }

    public static function form(Schema $schema): Schema
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
                TextColumn::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.columns.project'))
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.filters.project'))
                    ->relationship('project', 'name')
                    ->hiddenOn(TaskStagesRelationManager::class)
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Group::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.groups.project')),
                Group::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/task-stage.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.restore.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (TaskStage $record) {
                        try {
                            $record->forceDelete();
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.title'))
                                    ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.title'))
                                    ->body(__('projects::filament/clusters/configurations/resources/task-stage.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTaskStages::route('/'),
        ];
    }
}
