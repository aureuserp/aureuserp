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
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ProjectStageResource\Pages\ManageProjectStages;
use Webkul\Project\Models\ProjectStage;
use Webkul\Project\Settings\TaskSettings;

class ProjectStageResource extends Resource
{
    protected static ?string $model = ProjectStage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/project-stage.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(TaskSettings::class)->enable_project_stages;
    }

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/project-stage.table.columns.name'))
                    ->searchable()
                    ->sortable(),
            ])
            ->groups([
                Group::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/project-stage.table.columns.created-at'))
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
                            ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.restore.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (ProjectStage $record) {
                        try {
                            $record->forceDelete();

                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.success.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.error.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.error.body'))
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
                                ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.success.title'))
                                    ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.error.title'))
                                    ->body(__('projects::filament/clusters/configurations/resources/project-stage.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProjectStages::route('/'),
        ];
    }
}
