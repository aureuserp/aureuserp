<?php

namespace Webkul\Project\Filament\Resources\Tasks\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\Projects\Pages\ManageTasks;
use Webkul\Project\Filament\Resources\Tasks\TaskResource;
use Webkul\Project\Models\Task;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;
use Webkul\Support\Filament\Tables\Columns\ProgressBarEntry;

class TasksTable
{
    use HasCustomFields;

    public static function getModel(): string
    {
        return TaskResource::class;
    }

    public static function configure(Table $table): Table
    {
        $isTimesheetEnabled = app(TimeSettings::class)->enable_timesheets;

        return $table
            ->columns(static::mergeCustomTableColumns([
                TextColumn::make('id')
                    ->label(__('projects::filament/resources/task.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('priority')
                    ->label(__('projects::filament/resources/task.table.columns.priority'))
                    ->icon(fn (Task $record): string => $record->priority ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Task $record): string => $record->priority ? 'warning' : 'gray')
                    ->action(function (Task $record): void {
                        $record->update([
                            'priority' => ! $record->priority,
                        ]);
                    }),
                IconColumn::make('state')
                    ->label(__('projects::filament/resources/task.table.columns.state'))
                    ->sortable()
                    ->toggleable()
                    ->icon(fn (string $state): string => TaskState::icons()[$state])
                    ->color(fn (string $state): string => TaskState::colors()[$state])
                    ->tooltip(fn (string $state): string => TaskState::options()[$state])
                    ->action(
                        Action::make('updateState')
                            ->modalHeading('Update Task State')
                            ->schema(fn (Task $record): array => [
                                ToggleButtons::make('state')
                                    ->label(__('projects::filament/resources/task.table.columns.new-state'))
                                    ->required()
                                    ->default($record->state)
                                    ->inline()
                                    ->options(TaskState::options())
                                    ->colors(TaskState::colors())
                                    ->icons(TaskState::icons()),
                            ])
                            ->modalSubmitActionLabel(__('projects::filament/resources/task.table.columns.update-state'))
                            ->action(function (Task $record, array $data): void {
                                $record->update([
                                    'state' => $data['state'],
                                ]);
                            })
                    ),
                TextColumn::make('title')
                    ->label(__('projects::filament/resources/task.table.columns.title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('project.name')
                    ->label(__('projects::filament/resources/task.table.columns.project'))
                    ->hiddenOn(ManageTasks::class)
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder(__('projects::filament/resources/task.table.columns.project-placeholder')),
                TextColumn::make('milestone.name')
                    ->label(__('projects::filament/resources/task.table.columns.milestone'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (TaskSettings $taskSettings) => $taskSettings->enable_milestones),
                TextColumn::make('partner.name')
                    ->label(__('projects::filament/resources/task.table.columns.customer'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('users.name')
                    ->label(__('projects::filament/resources/task.table.columns.assignees'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('allocated_hours')
                    ->label(__('projects::filament/resources/task.table.columns.allocated-time'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.allocated-time'))
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                TextColumn::make('total_hours_spent')
                    ->label(__('projects::filament/resources/task.table.columns.time-spent'))
                    ->sortable()
                    ->toggleable()
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.time-spent'))
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                TextColumn::make('remaining_hours')
                    ->label(__('projects::filament/resources/task.table.columns.time-remaining'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($state) {
                        $hours = floor($state);
                        $minutes = ($state - $hours) * 60;

                        return $hours.':'.$minutes;
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('projects::filament/resources/task.table.columns.time-remaining'))
                            ->numeric()
                            ->numeric()
                            ->formatStateUsing(function ($state) {
                                $hours = floor($state);
                                $minutes = ($state - $hours) * 60;

                                return $hours.':'.$minutes;
                            })
                    )
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                ProgressBarEntry::make('progress')
                    ->label(__('projects::filament/resources/task.table.columns.progress'))
                    ->sortable()
                    ->toggleable()
                    ->color(fn (Task $record): string => $record->progress > 100 ? 'danger' : ($record->progress < 100 ? 'warning' : 'success'))
                    ->visible(fn (TimeSettings $timeSettings) => $timeSettings->enable_timesheets),
                TextColumn::make('deadline')
                    ->label(__('projects::filament/resources/task.table.columns.deadline'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tags.name')
                    ->label(__('projects::filament/resources/task.table.columns.tags'))
                    ->badge()
                    ->state(function (Task $record): array {
                        return $record->tags()->get()->map(fn ($tag) => [
                            'label' => $tag->name,
                            'color' => $tag->color ?? '#808080',
                        ])->toArray();
                    })
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state['label'])
                    ->color(fn ($state) => Color::generateV3Palette($state['color']))
                    ->toggleable(),
                TextColumn::make('stage.name')
                    ->label(__('projects::filament/resources/task.table.columns.stage'))
                    ->sortable()
                    ->toggleable(),
            ]))
            ->groups([
                Tables\Grouping\Group::make('state')
                    ->label(__('projects::filament/resources/task.table.groups.state'))
                    ->getTitleFromRecordUsing(fn (Task $record): string => TaskState::options()[$record->state]),
                Tables\Grouping\Group::make('project.name')
                    ->label(__('projects::filament/resources/task.table.groups.project')),
                Tables\Grouping\Group::make('deadline')
                    ->label(__('projects::filament/resources/task.table.groups.deadline'))
                    ->date(),
                Tables\Grouping\Group::make('stage.name')
                    ->label(__('projects::filament/resources/task.table.groups.stage')),
                Tables\Grouping\Group::make('milestone.name')
                    ->label(__('projects::filament/resources/task.table.groups.milestone')),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('projects::filament/resources/task.table.groups.customer')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('projects::filament/resources/task.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc')
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect(static::mergeCustomTableQueryBuilderConstraints([
                        TextConstraint::make('title')
                            ->label(__('projects::filament/resources/task.table.filters.title')),
                        SelectConstraint::make('priority')
                            ->label(__('projects::filament/resources/task.table.filters.priority'))
                            ->options([
                                0 => __('projects::filament/resources/task.table.filters.low'),
                                1 => __('projects::filament/resources/task.table.filters.high'),
                            ])
                            ->icon('heroicon-o-star'),
                        SelectConstraint::make('state')
                            ->label(__('projects::filament/resources/task.table.filters.state'))
                            ->multiple()
                            ->options(TaskState::options())
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('tags')
                            ->label(__('projects::filament/resources/task.table.filters.tags'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-tag'),
                        $isTimesheetEnabled
                        ? NumberConstraint::make('allocated_hours')
                            ->label(__('projects::filament/resources/task.table.filters.allocated-hours'))
                            ->icon('heroicon-o-clock')
                        : null,
                        $isTimesheetEnabled
                        ? NumberConstraint::make('total_hours_spent')
                            ->label(__('projects::filament/resources/task.table.filters.total-hours-spent'))
                            ->icon('heroicon-o-clock')
                        : null,
                        $isTimesheetEnabled
                        ? NumberConstraint::make('remaining_hours')
                            ->label(__('projects::filament/resources/task.table.filters.remaining-hours'))
                            ->icon('heroicon-o-clock')
                        : null,
                        $isTimesheetEnabled
                        ? NumberConstraint::make('overtime')
                            ->label(__('projects::filament/resources/task.table.filters.overtime'))
                            ->icon('heroicon-o-clock')
                        : null,
                        $isTimesheetEnabled
                        ? NumberConstraint::make('progress')
                            ->label(__('projects::filament/resources/task.table.filters.progress'))
                            ->icon('heroicon-o-bars-2')
                        : null,
                        DateConstraint::make('deadline')
                            ->label(__('projects::filament/resources/task.table.filters.deadline'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('created_at')
                            ->label(__('projects::filament/resources/task.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('projects::filament/resources/task.table.filters.updated-at')),
                        RelationshipConstraint::make('users')
                            ->label(__('projects::filament/resources/task.table.filters.assignees'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-users'),
                        RelationshipConstraint::make('partner')
                            ->label(__('projects::filament/resources/task.table.filters.customer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('project')
                            ->label(__('projects::filament/resources/task.table.filters.project'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-folder'),
                        RelationshipConstraint::make('stage')
                            ->label(__('projects::filament/resources/task.table.filters.stage'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('milestone')
                            ->label(__('projects::filament/resources/task.table.filters.milestone'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-flag'),
                        RelationshipConstraint::make('company')
                            ->label(__('projects::filament/resources/task.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('creator')
                            ->label(__('projects::filament/resources/task.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ]))->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/task.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/task.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => TaskResource::can('delete', $record) || TaskResource::can('forceDelete', $record) || TaskResource::can('restore', $record),
            );
    }
}
