<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource\Pages;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\ProjectResource\RelationManagers\MilestonesRelationManager;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Settings\TaskSettings;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/milestone.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(TaskSettings::class)->enable_milestones;
    }

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('deadline')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.deadline'))
                    ->dateTime()
                    ->sortable(),
                ToggleColumn::make('is_completed')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.is-completed'))
                    ->beforeStateUpdated(function ($record, $state) {
                        $record->completed_at = $state ? now() : null;
                    })
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.completed-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.project'))
                    ->hiddenOn([
                        MilestonesRelationManager::class,
                        ManageMilestones::class,
                    ])
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.creator'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('project.name')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.groups.project')),
                Group::make('is_completed')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.groups.is-completed')),
                Group::make('created_at')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                TernaryFilter::make('is_completed')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.filters.is-completed')),
                SelectFilter::make('project_id')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.filters.project'))
                    ->relationship('project', 'name')
                    ->hiddenOn([
                        MilestonesRelationManager::class,
                        ManageMilestones::class,
                    ])
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('projects::filament/clusters/configurations/resources/milestone.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/milestone.table.actions.edit.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/milestone.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/clusters/configurations/resources/milestone.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/clusters/configurations/resources/milestone.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/clusters/configurations/resources/milestone.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/clusters/configurations/resources/milestone.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMilestones::route('/'),
        ];
    }
}
