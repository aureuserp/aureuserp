<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Project\Enums\ProjectVisibility;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageMilestones;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use Webkul\Project\Models\Project;

class ProjectsTable
{
    public static function configure(Table $table, array $customColumns = [], array $customConstraints = []): Table
    {
        return $table
            ->columns(array_merge([
                Stack::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('projects::filament/resources/project.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                    ]),
                    Stack::make([
                        TextColumn::make('partner.name')
                            ->label(__('projects::filament/resources/project.table.columns.customer'))
                            ->icon('heroicon-o-phone')
                            ->tooltip(__('projects::filament/resources/project.table.columns.customer'))
                            ->sortable(),
                    ])
                        ->visible(fn (Project $record) => filled($record->partner)),
                    Stack::make([
                        TextColumn::make('start_date')
                            ->label(__('projects::filament/resources/project.table.columns.start-date'))
                            ->sortable()
                            ->extraAttributes(['class' => 'hidden']),
                        TextColumn::make('end_date')
                            ->label(__('projects::filament/resources/project.table.columns.end-date'))
                            ->sortable()
                            ->extraAttributes(['class' => 'hidden']),
                        TextColumn::make('planned_date')
                            ->icon('heroicon-o-calendar')
                            ->tooltip(__('projects::filament/resources/project.table.columns.planned-date'))
                            ->state(fn (Project $record): string => $record->start_date->format('d M Y').' - '.$record->end_date->format('d M Y')),
                    ])
                        ->visible(fn (Project $record) => filled($record->start_date) && filled($record->end_date)),
                    Stack::make([
                        TextColumn::make('remaining_hours')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('success')
                            ->color(fn (Project $record): string => $record->remaining_hours < 0 ? 'danger' : 'success')
                            ->state(fn (Project $record): string => $record->remaining_hours.' Hours')
                            ->tooltip(__('projects::filament/resources/project.table.columns.remaining-hours')),
                    ])
                        ->visible(fn (Project $record) => ProjectResource::getTimeSettings()->enable_timesheets && $record->allow_milestones && $record->remaining_hours),
                    Stack::make([
                        TextColumn::make('user.name')
                            ->label(__('projects::filament/resources/project.table.columns.project-manager'))
                            ->icon('heroicon-o-user')
                            ->label(__('projects::filament/resources/project.table.columns.project-manager'))
                            ->sortable(),
                    ])
                        ->visible(fn (Project $record) => filled($record->user)),
                    Stack::make([
                        TextColumn::make('tags.name')
                            ->badge()
                            ->state(function (Project $record): array {
                                return $record->tags->map(fn ($tag) => [
                                    'label' => $tag->name,
                                    'color' => $tag->color ?? '#808080',
                                ])->toArray();
                            })
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state['label'])
                            ->color(fn ($state) => Color::generateV3Palette($state['color']))
                            ->weight(FontWeight::Bold),
                    ])
                        ->visible(fn (Project $record): bool => (bool) $record->tags?->count()),
                ])
                    ->space(3),
            ], $customColumns))
            ->groups([
                Tables\Grouping\Group::make('stage.name')
                    ->label(__('projects::filament/resources/project.table.groups.stage')),
                Tables\Grouping\Group::make('user.name')
                    ->label(__('projects::filament/resources/project.table.groups.project-manager')),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('projects::filament/resources/project.table.groups.customer')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('projects::filament/resources/project.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort', direction: 'desc')
            ->defaultSort('sort', 'desc')
            ->filters([
                QueryBuilder::make()
                    ->constraints(array_merge([
                        TextConstraint::make('name')
                            ->label(__('projects::filament/resources/project.table.filters.name')),
                        SelectConstraint::make('visibility')
                            ->label(__('projects::filament/resources/project.table.filters.visibility'))
                            ->multiple()
                            ->options(ProjectVisibility::options())
                            ->icon('heroicon-o-eye'),
                        DateConstraint::make('start_date')
                            ->label(__('projects::filament/resources/project.table.filters.start-date')),
                        DateConstraint::make('end_date')
                            ->label(__('projects::filament/resources/project.table.filters.end-date')),
                        BooleanConstraint::make('allow_timesheets')
                            ->label(__('projects::filament/resources/project.table.filters.allow-timesheets'))
                            ->icon('heroicon-o-clock'),
                        BooleanConstraint::make('allow_milestones')
                            ->label(__('projects::filament/resources/project.table.filters.allow-milestones'))
                            ->icon('heroicon-o-flag'),
                        NumberConstraint::make('allocated_hours')
                            ->label(__('projects::filament/resources/project.table.filters.allocated-hours'))
                            ->icon('heroicon-o-clock'),
                        DateConstraint::make('created_at')
                            ->label(__('projects::filament/resources/project.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('projects::filament/resources/project.table.filters.updated-at')),
                        RelationshipConstraint::make('stage')
                            ->label(__('projects::filament/resources/project.table.filters.stage'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('partner')
                            ->label(__('projects::filament/resources/project.table.filters.customer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('projects::filament/resources/project.table.filters.project-manager'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('company')
                            ->label(__('projects::filament/resources/project.table.filters.company'))
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
                            ->label(__('projects::filament/resources/project.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('tags')
                            ->label(__('projects::filament/resources/project.table.filters.tags'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-tag'),
                    ], $customConstraints)),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActivityTableAction::make(),

                Action::make('is_favorite_by_user')
                    ->hiddenLabel()
                    ->icon(fn (Project $record): string => $record->is_favorite_by_user ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color(fn (Project $record): string => $record->is_favorite_by_user ? 'warning' : 'gray')
                    ->size('xl')
                    ->action(function (Project $record): void {
                        $record->favoriteUsers()->toggle([Auth::id()]);

                        $record->load(['favoriteUsers' => function ($query) {
                            $query->where('user_id', Auth::id());
                        }]);
                    }),

                Action::make('tasks')
                    ->label(fn (Project $record): string => __('projects::filament/resources/project.table.actions.tasks', ['count' => $record->tasks->whereNull('parent_id')->count()]))
                    ->icon('heroicon-m-clipboard-document-list')
                    ->color('gray')
                    ->url('https:example.com/tasks/{record}')
                    ->hidden(fn ($record) => $record->trashed())
                    ->url(fn (Project $record): string => ManageTasks::getUrl(['record' => $record])),

                Action::make('milestones')
                    ->label(fn (Project $record): string => $record->milestones->where('is_completed', true)->count().'/'.$record->milestones->count())
                    ->icon('heroicon-m-flag')
                    ->color('gray')
                    ->tooltip(fn (Project $record): string => __('projects::filament/resources/project.table.actions.milestones', ['completed' => $record->milestones->where('is_completed', true)->count(), 'all' => $record->milestones->count()]))
                    ->url('https:example.com/tasks/{record}')
                    ->hidden(fn (Project $record) => $record->trashed())
                    ->visible(fn (Project $record) => ProjectResource::getTaskSettings()->enable_milestones && $record->allow_milestones)
                    ->url(fn (Project $record): string => ManageMilestones::getUrl(['record' => $record])),

                ActionGroup::make([
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/project.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/project.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->action(function (Model $record) {
                            try {
                                $record->forceDelete();
                                Notification::make()
                                    ->success()
                                    ->title(__('projects::filament/resources/project.table.actions.force-delete.notification.success.title'))
                                    ->body(__('projects::filament/resources/project.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException $th) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('projects::filament/resources/project.table.actions.force-delete.notification.error.title'))
                                    ->body(__('projects::filament/resources/project.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ])
                    ->link()
                    ->hiddenLabel(),
            ])
            ->recordUrl(fn (Project $record): string => ProjectResource::getUrl('view', ['record' => $record]))
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with([
                    'milestones',
                    'favoriteUsers' => function ($query) {
                        $query->where('user_id', Auth::id());
                    },
                ]);
            });
    }
}
