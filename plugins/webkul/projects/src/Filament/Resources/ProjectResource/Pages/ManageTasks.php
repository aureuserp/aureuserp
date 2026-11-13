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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Enums\TaskViewLayout;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource;
use Webkul\Project\Filament\Concerns\HasTaskGraph;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Filament\Resources\TaskResource\Schemas\TaskForm;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageTasks extends ManageRelatedRecords
{
    use HasTableViews, HasTaskGraph;

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
            ->groups($this->getTaskGroups())
            ->emptyState(function () {
                if ($this->isTaskViewLayout(TaskViewLayout::KANBAN)) {
                    return view('projects::filament.kanban.board');
                }

                if ($this->isTaskViewLayout(TaskViewLayout::GRAPH)) {
                    return view('projects::filament.graph');
                }

                return null;
            })
            ->loadingSkeleton(fn (): bool => $this->isTaskViewLayout(TaskViewLayout::LIST));

        if (! $this->isTaskViewLayout(TaskViewLayout::LIST)) {
            $table = $table
                ->paginated(false)
                ->reorderable(null)
                ->toolbarActions([]);
        }

        return $table;
    }

    protected function getTaskGroups(): array
    {
        return [
            TableGroup::make('stage')
                ->label(__('projects::filament/kanban.group.stage'))
                ->column('stage_id')
                ->getTitleFromRecordUsing(fn (Task $record): string => $record->stage?->name ?? __('projects::filament/kanban.column.none')),

            TableGroup::make('milestone')
                ->label(__('projects::filament/kanban.group.milestone'))
                ->column('milestone_id')
                ->getTitleFromRecordUsing(fn (Task $record): string => $record->milestone?->name ?? __('projects::filament/kanban.column.none')),

            TableGroup::make('partner')
                ->label(__('projects::filament/kanban.group.partner'))
                ->column('partner_id')
                ->getTitleFromRecordUsing(fn (Task $record): string => $record->partner?->name ?? __('projects::filament/kanban.column.none')),

            TableGroup::make('state')
                ->label(__('projects::filament/kanban.group.state'))
                ->column('state')
                ->getTitleFromRecordUsing(fn (Task $record): string => $record->state?->getLabel() ?? __('projects::filament/kanban.column.none')),

            TableGroup::make('priority')
                ->label(__('projects::filament/kanban.group.priority'))
                ->column('priority')
                ->getTitleFromRecordUsing(fn (Task $record): string => $record->priority ? __('projects::filament/kanban.priority.high') : __('projects::filament/kanban.priority.normal')),
        ];
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->isTaskViewLayout(TaskViewLayout::LIST) ? parent::getTableRecords() : new Collection;
    }

    public function getKanbanGrouping(): string
    {
        $active = (string) str((string) $this->tableGrouping)->before(':');

        return in_array($active, ['stage', 'milestone', 'partner', 'state', 'priority'], true) ? $active : 'stage';
    }

    public function getKanbanColumns(): array
    {
        $tasks = $this->modifyQueryWithActiveTab(
            Task::query()
                ->where('project_id', $this->getOwnerRecord()->getKey())
                ->whereNull('parent_id')
        )
            ->with(['users.partner', 'tags', 'milestone', 'partner', 'stage', 'activities.activityType'])
            ->orderBy('sort')
            ->get();

        return match ($this->getKanbanGrouping()) {
            'milestone' => $this->kanbanRelationColumns($tasks, 'milestone_id', Milestone::class),
            'partner'   => $this->kanbanRelationColumns($tasks, 'partner_id', Partner::class),
            'state'     => $this->kanbanStateColumns($tasks),
            'priority'  => $this->kanbanPriorityColumns($tasks),
            default     => $this->kanbanStageColumns($tasks),
        };
    }

    protected function kanbanPriorityColumns(Collection $tasks): array
    {
        $grouped = $tasks->groupBy(fn (Task $task): string => $task->priority ? '1' : '0');

        return [
            [
                'value' => '1',
                'label' => __('projects::filament/kanban.priority.high'),
                'type'  => 'generic',
                'tasks' => $grouped->get('1', new Collection),
            ],
            [
                'value' => '0',
                'label' => __('projects::filament/kanban.priority.normal'),
                'type'  => 'generic',
                'tasks' => $grouped->get('0', new Collection),
            ],
        ];
    }

    protected function kanbanStageColumns(Collection $tasks): array
    {
        $grouped = $tasks->groupBy(fn (Task $task): string => $task->stage_id ? (string) $task->stage_id : 'none');

        $columns = TaskStage::query()
            ->where('project_id', $this->getOwnerRecord()->getKey())
            ->orderBy('sort')
            ->get()
            ->map(fn (TaskStage $stage): array => [
                'value' => (string) $stage->getKey(),
                'label' => $stage->name,
                'type'  => 'stage',
                'tasks' => $grouped->get((string) $stage->getKey(), new Collection),
            ])
            ->all();

        return $this->prependNoneColumn($columns, $grouped);
    }

    protected function kanbanRelationColumns(Collection $tasks, string $foreignKey, string $model): array
    {
        $grouped = $tasks->groupBy(fn (Task $task): string => $task->{$foreignKey} ? (string) $task->{$foreignKey} : 'none');

        $columns = $model::query()
            ->whereIn('id', $grouped->keys()->reject(fn (string $key): bool => $key === 'none')->all())
            ->orderBy('name')
            ->get()
            ->map(fn ($record): array => [
                'value' => (string) $record->getKey(),
                'label' => $record->name,
                'type'  => 'generic',
                'tasks' => $grouped->get((string) $record->getKey(), new Collection),
            ])
            ->all();

        return $this->prependNoneColumn($columns, $grouped);
    }

    protected function kanbanStateColumns(Collection $tasks): array
    {
        $grouped = $tasks->groupBy(fn (Task $task): string => $task->state?->value ?? 'none');

        $columns = [];

        foreach (TaskState::cases() as $state) {
            $columns[] = [
                'value' => $state->value,
                'label' => $state->getLabel(),
                'type'  => 'generic',
                'tasks' => $grouped->get($state->value, new Collection),
            ];
        }

        return $this->prependNoneColumn($columns, $grouped);
    }

    protected function prependNoneColumn(array $columns, SupportCollection $grouped): array
    {
        if (! $grouped->has('none')) {
            return $columns;
        }

        return [
            [
                'value' => 'none',
                'label' => __('projects::filament/kanban.column.none'),
                'type'  => 'none',
                'tasks' => $grouped->get('none'),
            ],
            ...$columns,
        ];
    }

    public function kanbanStateProgress(Collection $tasks): array
    {
        $counts = [];

        foreach (TaskState::cases() as $state) {
            $counts[$state->value] = 0;
        }

        foreach ($tasks as $task) {
            $value = $task->state?->value;

            if ($value !== null && array_key_exists($value, $counts)) {
                $counts[$value]++;
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
        $groups = collect($this->getKanbanColumns())
            ->filter(fn (array $column): bool => $column['tasks']->isNotEmpty())
            ->map(fn (array $column): array => [
                'label' => $column['label'],
                'tasks' => $column['tasks'],
            ])
            ->values()
            ->all();

        return $this->buildTaskGraphData($groups);
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

        $grouping = $this->getKanbanGrouping();

        $orderedIds = array_values(array_map('strval', array_filter($orderedIds, 'is_scalar')));

        DB::transaction(function () use ($task, $grouping, $newColumnValue, $orderedIds) {
            $this->assignKanbanColumn($task, $grouping, $newColumnValue);

            $task->save();

            $this->persistKanbanOrder($orderedIds);
        });

        $this->dispatch('kanban-stats-refresh');

        $this->skipRender();
    }

    protected function persistKanbanOrder(array $orderedIds): void
    {
        if ($orderedIds === []) {
            return;
        }

        $cases = '';

        foreach ($orderedIds as $position => $id) {
            $cases .= ' WHEN '.(int) $id.' THEN '.(int) $position;
        }

        Task::whereIn('id', array_map('intval', $orderedIds))
            ->update(['sort' => DB::raw('CASE id'.$cases.' END')]);
    }

    protected function assignKanbanColumn(Task $task, string $grouping, string $value): void
    {
        if ($value === 'none') {
            return;
        }

        if ($grouping === 'stage') {
            $stage = TaskStage::query()
                ->where('project_id', $this->getOwnerRecord()->getKey())
                ->whereKey($value)
                ->first();

            if ($stage) {
                $task->stage_id = $stage->getKey();
            }

            return;
        }

        if ($grouping === 'state') {
            $state = TaskState::tryFrom($value);

            if ($state) {
                $task->state = $state;
            }

            return;
        }

        if ($grouping === 'priority') {
            $task->priority = $value === '1';

            return;
        }

        $column = match ($grouping) {
            'milestone' => 'milestone_id',
            'partner'   => 'partner_id',
            default     => null,
        };

        if ($column !== null) {
            $task->{$column} = (int) $value;
        }
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

    public function taskActivitiesAction(): Action
    {
        return Action::make('taskActivities')
            ->record(fn (array $arguments): ?Task => $this->findTask(
                isset($arguments['record']) ? (string) $arguments['record'] : null,
            ))
            ->authorize(fn (?Task $record): bool => $record && TaskResource::canView($record))
            ->modalHeading(__('projects::filament/kanban.activities.heading'))
            ->modalDescription(fn (Task $record): ?string => $record->title)
            ->modalWidth('2xl')
            ->modalSubmitAction(false)
            ->schema([
                RepeatableEntry::make('activities')
                    ->hiddenLabel()
                    ->state(fn (Task $record): array => $record->activities
                        ->sortBy(fn ($activity): int => $activity->date_deadline?->timestamp ?? PHP_INT_MAX)
                        ->values()
                        ->all())
                    ->schema([
                        TextEntry::make('activityType.name')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->icon(fn ($record): string => preg_match('/^heroicon-[osm]-/', (string) $record->activityType?->icon) ? $record->activityType->icon : 'heroicon-o-clock')
                            ->placeholder('—'),

                        TextEntry::make('date_deadline')
                            ->label(__('projects::filament/kanban.activities.deadline'))
                            ->date()
                            ->placeholder('—')
                            ->alignEnd(),

                        TextEntry::make('summary')
                            ->hiddenLabel()
                            ->placeholder('—')
                            ->columnSpanFull(),

                        TextEntry::make('assignedTo.name')
                            ->label(__('projects::filament/kanban.activities.assigned-to'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->extraModalFooterActions(fn (Task $record): array => [
                Action::make('scheduleAnotherTaskActivity')
                    ->label(__('projects::filament/kanban.activities.schedule'))
                    ->icon('heroicon-o-plus')
                    ->visible(fn (): bool => TaskResource::canEdit($record))
                    ->action(fn () => $this->replaceMountedAction('scheduleTaskActivity', ['record' => $record->getKey()])),
            ]);
    }

    public function assignTaskAction(): Action
    {
        return Action::make('assignTask')
            ->record(fn (array $arguments): ?Task => $this->findTask(
                isset($arguments['record']) ? (string) $arguments['record'] : null,
            ))
            ->authorize(fn (?Task $record): bool => $record && TaskResource::canEdit($record))
            ->modalHeading(__('projects::filament/kanban.assign.heading'))
            ->modalWidth('sm')
            ->modalSubmitActionLabel(__('projects::filament/kanban.assign.save'))
            ->fillForm(fn (Task $record): array => ['users' => $record->users->pluck('id')->all()])
            ->schema([
                Select::make('users')
                    ->label(__('projects::filament/kanban.assign.label'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->action(function (array $data, Task $record): void {
                $record->users()->sync($data['users'] ?? []);

                Notification::make()
                    ->success()
                    ->title(__('projects::filament/kanban.notifications.assignees-updated'))
                    ->send();
            });
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
            ->fillForm(fn (array $arguments): array => array_merge(
                ['project_id' => $this->getOwnerRecord()->getKey()],
                $this->kanbanCreatePrefill(
                    (string) ($arguments['group'] ?? 'stage'),
                    isset($arguments['value']) ? (string) $arguments['value'] : null,
                ),
            ))
            ->authorize(fn (): bool => TaskResource::canCreate())
            ->successNotificationTitle(__('projects::filament/kanban.notifications.task-created'));
    }

    protected function kanbanCreatePrefill(string $group, ?string $value): array
    {
        if ($value === null || $value === 'none') {
            return [];
        }

        return match ($group) {
            'milestone' => ['milestone_id' => (int) $value],
            'partner'   => ['partner_id' => (int) $value],
            'state'     => ['state' => $value],
            'priority'  => ['priority' => $value === '1'],
            default     => ['stage_id' => (int) $value],
        };
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
