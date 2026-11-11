<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Enums\TaskViewLayout;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Filament\Resources\TaskResource\Schemas\TaskForm;
use Webkul\Project\Filament\Widgets\TaskGraphChart;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageTasks extends ManageRelatedRecords
{
    use HasTableViews;

    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'tasks';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    #[Url(as: 'layout')]
    public ?string $viewLayout = null;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/project/pages/manage-tasks.title');
    }

    public function getTaskViewLayouts(): array
    {
        return [TaskViewLayout::LIST, TaskViewLayout::KANBAN, TaskViewLayout::GRAPH];
    }

    public function getTaskViewLayout(): TaskViewLayout
    {
        $layout = TaskViewLayout::tryFrom((string) $this->viewLayout);

        return in_array($layout, $this->getTaskViewLayouts(), true) ? $layout : $this->getTaskViewLayouts()[0];
    }

    public function isTaskViewLayout(TaskViewLayout $layout): bool
    {
        return $this->getTaskViewLayout() === $layout;
    }

    public function switchTaskViewLayout(string $layout): void
    {
        $target = TaskViewLayout::tryFrom($layout);

        $this->viewLayout = (in_array($target, $this->getTaskViewLayouts(), true) ? $target : $this->getTaskViewLayouts()[0])->value;

        $this->bootedInteractsWithTable();

        $this->resetPage();

        $this->flushCachedTableRecords();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('projects::filament/resources/project/pages/manage-tasks.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(TaskResource::getUrl('create')),
        ];
    }

    public function table(Table $table): Table
    {
        $table = TaskResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (Task $record): string => TaskResource::getUrl('view', ['record' => $record]))
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->url(fn (Task $record): string => TaskResource::getUrl('edit', ['record' => $record]))
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project/pages/manage-tasks.table.actions.restore.notification.title'))
                                ->body(__('projects::filament/resources/project/pages/manage-tasks.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project/pages/manage-tasks.table.actions.delete.notification.title'))
                                ->body(__('projects::filament/resources/project/pages/manage-tasks.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('projects::filament/resources/project/pages/manage-tasks.table.actions.force-delete.notification.title'))
                                ->body(__('projects::filament/resources/project/pages/manage-tasks.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereNull('parent_id');
            })
            ->emptyState(function () {
                if ($this->isTaskViewLayout(TaskViewLayout::KANBAN)) {
                    return view('projects::filament.kanban.board');
                }

                if ($this->isTaskViewLayout(TaskViewLayout::GRAPH)) {
                    return TaskGraphChart::embedGraph($this->getTaskGraph());
                }

                return null;
            })
            ->loadingSkeleton(fn (): bool => $this->isTaskViewLayout(TaskViewLayout::LIST));

        if (! $this->isTaskViewLayout(TaskViewLayout::LIST)) {
            $table = $table
                ->paginated(false)
                ->groups([])
                ->toolbarActions([]);
        }

        return $table;
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->isTaskViewLayout(TaskViewLayout::LIST) ? parent::getTableRecords() : new Collection;
    }

    public function getKanbanStages(): Collection
    {
        $stages = TaskStage::query()
            ->where('project_id', $this->getOwnerRecord()->getKey())
            ->orderBy('sort')
            ->get();

        $tasks = $this->modifyQueryWithActiveTab(
            Task::query()
                ->where('project_id', $this->getOwnerRecord()->getKey())
                ->whereNull('parent_id')
        )
            ->with(['users', 'tags', 'milestone', 'partner', 'activities.activityType'])
            ->orderBy('sort')
            ->get()
            ->groupBy('stage_id');

        $stages->each(fn (TaskStage $stage) => $stage->setRelation('tasks', $tasks->get($stage->getKey(), new Collection)));

        return $stages;
    }

    public function kanbanStageActivityProgress(TaskStage $stage): array
    {
        return $this->computeTaskActivityProgress($stage->tasks);
    }

    public function taskActivityIndicator(Task $task): array
    {
        if ($task->activities->isEmpty()) {
            return ['icon' => 'heroicon-o-clock', 'color' => 'gray'];
        }

        $today = Carbon::now()->startOfDay();

        $dated = $task->activities
            ->filter(fn ($activity): bool => filled($activity->date_deadline))
            ->sortBy(fn ($activity) => Carbon::parse($activity->date_deadline));

        $overdue = $dated->first(fn ($activity): bool => Carbon::parse($activity->date_deadline)->startOfDay()->lt($today));
        $todayActivity = $dated->first(fn ($activity): bool => Carbon::parse($activity->date_deadline)->startOfDay()->eq($today));
        $planned = $dated->first(fn ($activity): bool => Carbon::parse($activity->date_deadline)->startOfDay()->gt($today));

        [$activity, $color] = match (true) {
            (bool) $overdue       => [$overdue, 'danger'],
            (bool) $todayActivity => [$todayActivity, 'warning'],
            (bool) $planned       => [$planned, 'success'],
            default               => [$task->activities->first(), 'success'],
        };

        $icon = (string) ($activity?->activityType?->icon ?? '');

        return [
            'icon'  => preg_match('/^heroicon-[osm]-/', $icon) ? $icon : 'heroicon-o-clock',
            'color' => $color,
        ];
    }

    public function getTaskGraph(): array
    {
        $stages = TaskStage::query()
            ->where('project_id', $this->getOwnerRecord()->getKey())
            ->orderBy('sort')
            ->get();

        $labels = [];

        $data = [];

        foreach ($stages as $stage) {
            $labels[] = $stage->name;

            $data[] = $this->modifyQueryWithActiveTab(
                Task::query()
                    ->where('project_id', $this->getOwnerRecord()->getKey())
                    ->whereNull('parent_id')
                    ->where('stage_id', $stage->getKey())
            )->count();
        }

        return [
            'labels' => $labels,
            'data'   => $data,
            'label'  => TaskResource::getPluralModelLabel(),
        ];
    }

    protected function computeTaskActivityProgress(Collection $tasks): array
    {
        $counts = ['overdue' => 0, 'today' => 0, 'planned' => 0, 'none' => 0];

        $today = Carbon::now()->startOfDay();

        foreach ($tasks as $task) {
            $states = $task->activities
                ->map(function ($activity) use ($today): ?string {
                    if (blank($activity->date_deadline)) {
                        return null;
                    }

                    $deadline = Carbon::parse($activity->date_deadline)->startOfDay();

                    return $deadline->lt($today) ? 'overdue' : ($deadline->eq($today) ? 'today' : 'planned');
                })
                ->filter();

            if ($states->contains('overdue')) {
                $counts['overdue']++;
            } elseif ($states->contains('today')) {
                $counts['today']++;
            } elseif ($states->contains('planned')) {
                $counts['planned']++;
            } else {
                $counts['none']++;
            }
        }

        return $counts;
    }

    public function canManageTaskStages(): bool
    {
        return TaskStageResource::canCreate();
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        $task = Task::query()
            ->where('project_id', $this->getOwnerRecord()->getKey())
            ->whereKey($recordId)
            ->first();

        if (! $task || ! TaskResource::canEdit($task)) {
            return;
        }

        $stage = TaskStage::query()
            ->where('project_id', $this->getOwnerRecord()->getKey())
            ->whereKey($newColumnValue)
            ->first();

        if (! $stage) {
            return;
        }

        $orderedIds = array_values(array_map('strval', array_filter($orderedIds, 'is_scalar')));

        DB::transaction(function () use ($task, $stage, $orderedIds) {
            $task->stage_id = $stage->getKey();
            $task->save();

            foreach ($orderedIds as $position => $id) {
                Task::whereKey($id)->update(['sort' => $position]);
            }
        });

        Notification::make()
            ->success()
            ->title(__('projects::filament/kanban.notifications.moved'))
            ->send();
    }

    public function setTaskState(string $taskId, string $state): void
    {
        $case = TaskState::tryFrom($state);

        if (! $case) {
            return;
        }

        $task = Task::query()
            ->where('project_id', $this->getOwnerRecord()->getKey())
            ->whereKey($taskId)
            ->first();

        if (! $task || ! TaskResource::canEdit($task)) {
            return;
        }

        $task->update(['state' => $case]);

        Notification::make()
            ->success()
            ->title(__('projects::filament/kanban.notifications.state-updated'))
            ->send();
    }

    public function toggleTaskPriority(string $taskId): void
    {
        $task = Task::query()
            ->where('project_id', $this->getOwnerRecord()->getKey())
            ->whereKey($taskId)
            ->first();

        if (! $task || ! TaskResource::canEdit($task)) {
            return;
        }

        $task->update(['priority' => ! $task->priority]);
    }

    public function shareTask(string $taskId): void
    {
        $task = $this->findTask($taskId);

        if (! $task) {
            return;
        }

        $url = TaskResource::getUrl('view', ['record' => $task->getKey()]);

        $this->js('window.navigator.clipboard?.writeText('.json_encode($url).')');

        Notification::make()
            ->success()
            ->title(__('projects::filament/kanban.card.shared'))
            ->send();
    }

    public function scheduleTaskActivityAction(): Action
    {
        return ActivityTableAction::make('scheduleTaskActivity')
            ->record(fn (array $arguments): ?Task => $this->findTask(
                isset($arguments['record']) ? (string) $arguments['record'] : null,
            ))
            ->authorize(fn (?Task $record): bool => $record && TaskResource::canEdit($record));
    }

    public function deleteTaskAction(): Action
    {
        return Action::make('deleteTask')
            ->label(__('projects::filament/kanban.card.delete'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                $task = $this->findTask($arguments['task'] ?? null);

                if (! $task || ! TaskResource::canDelete($task)) {
                    return;
                }

                $task->delete();

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.task-deleted'))
                    ->send();
            });
    }

    public function quickCreateTaskAction(): CreateAction
    {
        return CreateAction::make('quickCreateTask')
            ->label(__('projects::filament/kanban.card.quick-create'))
            ->icon('heroicon-m-plus')
            ->modalHeading(__('projects::filament/kanban.card.quick-create'))
            ->modalWidth('7xl')
            ->model(Task::class)
            ->schema(fn (Schema $schema): Schema => TaskForm::configure($schema))
            ->fillForm(fn (array $arguments): array => [
                'project_id' => $this->getOwnerRecord()->getKey(),
                'stage_id'   => $arguments['stage'] ?? null,
            ])
            ->authorize(fn (): bool => TaskResource::canCreate())
            ->successNotificationTitle(__('projects::filament/kanban.notifications.task-created'));
    }

    public function addTaskStageAction(): Action
    {
        return Action::make('addTaskStage')
            ->label(__('projects::filament/kanban.column.add'))
            ->icon('heroicon-o-plus')
            ->modalHeading(__('projects::filament/kanban.column.add'))
            ->modalWidth('md')
            ->schema([
                TextInput::make('name')
                    ->label(__('projects::filament/kanban.column.name'))
                    ->required()
                    ->maxLength(255),
            ])
            ->authorize(fn (): bool => TaskStageResource::canCreate())
            ->action(function (array $data): void {
                $project = $this->getOwnerRecord();

                $maxSort = (int) TaskStage::query()
                    ->where('project_id', $project->getKey())
                    ->max('sort');

                TaskStage::create([
                    'name'       => $data['name'],
                    'project_id' => $project->getKey(),
                    'company_id' => $project->company_id,
                    'sort'       => $maxSort + 1,
                ]);

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.stage-created'))
                    ->send();
            });
    }

    public function editTaskStageAction(): Action
    {
        return Action::make('editTaskStage')
            ->label(__('projects::filament/kanban.column.edit'))
            ->icon('heroicon-o-pencil-square')
            ->modalWidth('md')
            ->schema([
                TextInput::make('name')
                    ->label(__('projects::filament/kanban.column.name'))
                    ->required()
                    ->maxLength(255),
            ])
            ->fillForm(fn (array $arguments): array => [
                'name' => $this->findTaskStage($arguments['stage'] ?? null)?->name,
            ])
            ->action(function (array $data, array $arguments): void {
                $stage = $this->findTaskStage($arguments['stage'] ?? null);

                if (! $stage || ! TaskStageResource::canEdit($stage)) {
                    return;
                }

                $stage->update(['name' => $data['name']]);

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.stage-updated'))
                    ->send();
            });
    }

    public function deleteTaskStageAction(): Action
    {
        return Action::make('deleteTaskStage')
            ->label(__('projects::filament/kanban.column.delete'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                $stage = $this->findTaskStage($arguments['stage'] ?? null);

                if (! $stage || ! TaskStageResource::canDelete($stage)) {
                    return;
                }

                if ($stage->tasks()->exists()) {
                    Notification::make()
                        ->danger()
                        ->title(__('projects::filament/kanban.notifications.stage-in-use'))
                        ->send();

                    return;
                }

                $stage->delete();

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.stage-deleted'))
                    ->send();
            });
    }

    protected function findTask(?string $value): ?Task
    {
        return filled($value)
            ? Task::query()
                ->where('project_id', $this->getOwnerRecord()->getKey())
                ->find($value)
            : null;
    }

    protected function findTaskStage(?string $value): ?TaskStage
    {
        return filled($value)
            ? TaskStage::query()
                ->where('project_id', $this->getOwnerRecord()->getKey())
                ->find($value)
            : null;
    }

    public function infolist(Schema $schema): Schema
    {
        return TaskResource::infolist($schema);
    }

    public function getPresetTableViews(): array
    {
        return [
            'open_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.open-tasks'))
                ->icon('heroicon-s-bolt')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [
                    TaskState::CANCELLED,
                    TaskState::DONE,
                ])),

            'my_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.my-tasks'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereHas('users', function ($q) {
                            $q->where('user_id', Auth::id());
                        });
                }),

            'unassigned_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.unassigned-tasks'))
                ->icon('heroicon-s-user-minus')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereDoesntHave('users');
                }),

            'closed_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.closed-tasks'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    TaskState::CANCELLED,
                    TaskState::DONE,
                ])),

            'starred_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.starred-tasks'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('priority', true)),

            'archived_tasks' => PresetView::make(__('projects::filament/resources/project/pages/manage-tasks.tabs.archived-tasks'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
