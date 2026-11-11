<?php

namespace Webkul\Project\Filament\Resources\TaskResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
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
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Filament\Resources\TaskResource\Schemas\TaskForm;
use Webkul\Project\Filament\Widgets\StatsOverviewWidget;
use Webkul\Project\Filament\Widgets\TaskGraphChart;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListTasks extends ListRecords
{
    use HasTableViews;

    protected static string $resource = TaskResource::class;

    #[Url(as: 'layout')]
    public ?string $viewLayout = null;

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::make(),
        ];
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

    public function table(Table $table): Table
    {
        $table = parent::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('parent_id'))
            ->emptyState(function () {
                if ($this->isTaskViewLayout(TaskViewLayout::KANBAN)) {
                    return view('projects::filament.kanban.board-project');
                }

                if ($this->isTaskViewLayout(TaskViewLayout::GRAPH)) {
                    return TaskGraphChart::embedGraph($this->getTaskGraph());
                }

                return null;
            });

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

    public function getKanbanProjectColumns(): array
    {
        $tasks = $this->modifyQueryWithActiveTab(
            Task::query()->whereNull('parent_id')
        )
            ->with(['users', 'tags', 'milestone', 'partner', 'project', 'stage', 'activities.activityType'])
            ->orderBy('sort')
            ->get()
            ->groupBy(fn (Task $task): string => $task->project_id ? (string) $task->project_id : 'private');

        $projectColumns = Project::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Project $project): array => [
                'value'   => (string) $project->getKey(),
                'label'   => $project->name,
                'private' => false,
                'tasks'   => $tasks->get((string) $project->getKey(), new Collection),
            ])
            ->all();

        return [
            [
                'value'   => 'private',
                'label'   => __('projects::filament/kanban.column.private'),
                'private' => true,
                'tasks'   => $tasks->get('private', new Collection),
            ],
            ...$projectColumns,
        ];
    }

    public function kanbanColumnActivityProgress(Collection $tasks): array
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
        $counts = $this->modifyQueryWithActiveTab(
            Task::query()
                ->whereNull('parent_id')
                ->whereNotNull('project_id')
        )
            ->get()
            ->groupBy(fn (Task $task): string => (string) $task->project_id);

        $projects = Project::query()
            ->whereIn('id', $counts->keys()->all())
            ->orderBy('name')
            ->get();

        $labels = [];

        $data = [];

        foreach ($projects as $project) {
            $labels[] = $project->name;

            $data[] = $counts->get((string) $project->getKey(), new Collection)->count();
        }

        return [
            'labels' => $labels,
            'data'   => $data,
            'label'  => TaskResource::getPluralModelLabel(),
        ];
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        $task = $this->findTask($recordId);

        if (! $task || ! TaskResource::canEdit($task)) {
            return;
        }

        $toPrivate = $newColumnValue === 'private';

        $project = $toPrivate ? null : Project::query()->find($newColumnValue);

        if (! $toPrivate && ! $project) {
            return;
        }

        $orderedIds = array_values(array_map('strval', array_filter($orderedIds, 'is_scalar')));

        DB::transaction(function () use ($task, $project, $toPrivate, $orderedIds) {
            if ($toPrivate) {
                if ($task->project_id !== null) {
                    $task->project_id = null;
                    $task->stage_id = null;
                }
            } elseif ((int) $task->project_id !== (int) $project->getKey()) {
                $task->project_id = $project->getKey();
                $task->stage_id = TaskResource::getDefaultStageId($project->getKey());
            }

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

        $task = $this->findTask($taskId);

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
        $task = $this->findTask($taskId);

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
                'project_id' => $arguments['project'] ?? null,
            ])
            ->authorize(fn (): bool => TaskResource::canCreate())
            ->successNotificationTitle(__('projects::filament/kanban.notifications.task-created'));
    }

    public function quickCreatePrivateTaskAction(): Action
    {
        return Action::make('quickCreatePrivateTask')
            ->label(__('projects::filament/kanban.card.quick-create'))
            ->icon('heroicon-m-plus')
            ->modalHeading(__('projects::filament/kanban.card.quick-create'))
            ->modalWidth('md')
            ->schema([
                TextInput::make('title')
                    ->label(__('projects::filament/kanban.card.title'))
                    ->required()
                    ->maxLength(255),
            ])
            ->authorize(fn (): bool => TaskResource::canCreate())
            ->action(function (array $data): void {
                Task::create([
                    'title'      => $data['title'],
                    'project_id' => null,
                    'stage_id'   => null,
                    'state'      => TaskState::IN_PROGRESS,
                ]);

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.task-created'))
                    ->send();
            });
    }

    public function addProjectAction(): CreateAction
    {
        return CreateAction::make('addProject')
            ->label(__('projects::filament/kanban.column.add-project'))
            ->icon('heroicon-o-plus')
            ->modalHeading(__('projects::filament/kanban.column.add-project'))
            ->modalWidth('md')
            ->model(Project::class)
            ->schema([
                TextInput::make('name')
                    ->label(__('projects::filament/kanban.column.name'))
                    ->required()
                    ->maxLength(255),
            ])
            ->authorize(fn (): bool => ProjectResource::canCreate())
            ->successNotificationTitle(__('projects::filament/kanban.notifications.project-created'));
    }

    public function canManageProjects(): bool
    {
        return ProjectResource::canCreate();
    }

    public function editProjectAction(): Action
    {
        return Action::make('editProject')
            ->label(__('projects::filament/kanban.column.edit-project'))
            ->icon('heroicon-o-pencil-square')
            ->modalWidth('md')
            ->schema([
                TextInput::make('name')
                    ->label(__('projects::filament/kanban.column.name'))
                    ->required()
                    ->maxLength(255),
            ])
            ->fillForm(fn (array $arguments): array => [
                'name' => $this->findProject($arguments['project'] ?? null)?->name,
            ])
            ->action(function (array $data, array $arguments): void {
                $project = $this->findProject($arguments['project'] ?? null);

                if (! $project || ! ProjectResource::canEdit($project)) {
                    return;
                }

                $project->update(['name' => $data['name']]);

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.project-updated'))
                    ->send();
            });
    }

    public function deleteProjectAction(): Action
    {
        return Action::make('deleteProject')
            ->label(__('projects::filament/kanban.column.delete-project'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                $project = $this->findProject($arguments['project'] ?? null);

                if (! $project || ! ProjectResource::canDelete($project)) {
                    return;
                }

                if ($project->tasks()->exists()) {
                    Notification::make()
                        ->danger()
                        ->title(__('projects::filament/kanban.notifications.project-in-use'))
                        ->send();

                    return;
                }

                $project->delete();

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.project-deleted'))
                    ->send();
            });
    }

    protected function findProject(?string $value): ?Project
    {
        return filled($value) ? Project::query()->find($value) : null;
    }

    protected function findTask(?string $value): ?Task
    {
        return filled($value) ? Task::query()->find($value) : null;
    }

    public function getPresetTableViews(): array
    {
        return [
            'open_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.open-tasks'))
                ->icon('heroicon-s-bolt')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('state', [
                    TaskState::CANCELLED,
                    TaskState::DONE,
                ])),

            'my_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.my-tasks'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereHas('users', function ($q) {
                            $q->where('user_id', Auth::id());
                        });
                }),

            'unassigned_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.unassigned-tasks'))
                ->icon('heroicon-s-user-minus')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereDoesntHave('users');
                }),

            'private_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.private-tasks'))
                ->icon('heroicon-s-lock-closed')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereNull('project_id');
                }),

            'followed_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.followed-tasks'))
                ->icon('heroicon-s-bell')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('followers', function ($q) {
                        $q->where('chatter_followers.partner_id', Auth::user()->partner_id);
                    });
                }),

            'closed_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.closed-tasks'))
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    TaskState::CANCELLED,
                    TaskState::DONE,
                ])),

            'starred_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.starred-tasks'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('priority', true)),

            'archived_tasks' => PresetView::make(__('projects::filament/resources/task/pages/list-tasks.tabs.archived-tasks'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('projects::filament/resources/task/pages/list-tasks.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
