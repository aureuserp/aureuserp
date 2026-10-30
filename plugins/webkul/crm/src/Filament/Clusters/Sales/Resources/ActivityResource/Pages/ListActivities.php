<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasKanbanLayout;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Concerns\ManagesActivityTypeKanbanColumns;
use Webkul\Crm\Filament\Concerns\ShowsRecordsGraph;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Filament\Widgets\RecordsChart;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityType;
use Webkul\TableViews\Filament\Components\PresetView;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class ListActivities extends ListRecords
{
    use HasCRMTableViews, HasKanbanBoard, HasKanbanLayout, HasViewLayouts, ManagesActivityTypeKanbanColumns, ShowsRecordsGraph {
        HasKanbanLayout::content insteadof HasKanbanBoard;
        HasKanbanLayout::getKanbanQuery insteadof HasKanbanBoard;
        HasKanbanLayout::getTableViewCountQuery insteadof HasCRMTableViews;
        kanbanColumnAction as kanbanColumnActionFromBoard;
    }

    public const GRAPH_GROUPS = [
        'activityType.name' => ['field' => 'activity_type_id', 'relation' => 'activityType'],
        'assignedTo.name'   => ['field' => 'assigned_to', 'relation' => 'assignedTo'],
        'date_deadline'     => ['month' => 'date_deadline'],
    ];

    protected static string $resource = ActivityResource::class;

    public array $kanbanActivityFilter = [];

    protected bool $ignoreKanbanActivityFilter = false;

    protected ?array $kanbanProgress = null;

    public function mount(): void
    {
        parent::mount();

        if ($this->isViewLayout(ViewLayout::KANBAN)) {
            $this->activeTableView ??= 'default';
        }
    }

    public function getViewLayouts(): array
    {
        return [ViewLayout::LIST, ViewLayout::KANBAN, ViewLayout::GRAPH];
    }

    protected function modifyKanbanQuery(Builder $query): Builder
    {
        if ($this->ignoreKanbanActivityFilter) {
            return $query;
        }

        foreach ($this->kanbanActivityFilter as $typeId => $state) {
            if (! ActivityDueStatus::tryFrom($state)) {
                continue;
            }

            $query->where(fn (Builder $query): Builder => $query
                ->where('activity_type_id', '!=', $typeId)
                ->orWhere(fn (Builder $query): Builder => $query->dueStatus($state)));
        }

        return $query;
    }

    public function toggleKanbanActivityFilter(string $typeId, string $state): void
    {
        if (! ActivityDueStatus::tryFrom($state)) {
            return;
        }

        if (($this->kanbanActivityFilter[$typeId] ?? null) === $state) {
            unset($this->kanbanActivityFilter[$typeId]);

            return;
        }

        $this->kanbanActivityFilter[$typeId] = $state;
    }

    public function kanbanColumnProgress(KanbanColumn $column): array
    {
        $this->kanbanProgress ??= $this->computeKanbanProgress();

        return $this->kanbanProgress[$column->value] ?? [];
    }

    protected function computeKanbanProgress(): array
    {
        $this->ignoreKanbanActivityFilter = true;

        try {
            $activities = $this->getKanbanQuery($this->getKanbanBoard())
                ->reorder()
                ->setEagerLoads([])
                ->get(['chatter_messages.id', 'chatter_messages.activity_type_id', 'chatter_messages.date_deadline']);
        } finally {
            $this->ignoreKanbanActivityFilter = false;
        }

        $progress = [];

        foreach ($activities as $activity) {
            $progress[(string) $activity->activity_type_id] ??= [];
            $state = $activity->getDueStatus()->value;
            $progress[(string) $activity->activity_type_id][$state] = ($progress[(string) $activity->activity_type_id][$state] ?? 0) + 1;
        }

        return $progress;
    }

    public function kanbanColumnAction(): Action
    {
        return $this->kanbanColumnActionFromBoard()
            ->fillForm(fn (array $arguments): array => ScheduleActivityAction::defaultsForType($arguments['column'] ?? null));
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.navigation.title');
    }

    public function getBreadcrumb(): string
    {
        return $this->getViewLayout()->getLabel();
    }

    public function table(Table $table): Table
    {
        $table = parent::table($table);

        return match ($this->getViewLayout()) {
            ViewLayout::KANBAN => $this->configureKanbanLayoutTable($table),
            ViewLayout::GRAPH  => $table
                ->paginated(false)
                ->emptyState(fn () => RecordsChart::embedGraph($this->getRecordsGraph())),
            default => $table,
        };
    }

    public function getRecordsGraph(): array
    {
        $group = self::GRAPH_GROUPS[(string) str((string) $this->tableGrouping)->before(':')] ?? self::GRAPH_GROUPS['activityType.name'];

        return $this->buildRecordsGraph(
            $this->filterTableQuery($this->modifyQueryWithActiveTab(ActivityResource::getEloquentQuery())),
            $group,
            ActivityResource::getPluralModelLabel(),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            ScheduleActivityAction::make(),
        ];
    }

    public function getPresetTableViews(): array
    {
        $mine = fn (Builder $query): Builder => $query->where('assigned_to', Auth::id());

        return [
            'my_activities' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.my-activities'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing($mine),

            'overdue' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.overdue'))
                ->icon('heroicon-s-exclamation-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $mine($query)->overdue()),

            'today' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.today'))
                ->icon('heroicon-s-clock')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $mine($query)->dueToday()),

            'planned' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.planned'))
                ->icon('heroicon-s-calendar-days')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $mine($query)->planned()),

            'all' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.all'))
                ->icon('heroicon-s-users')
                ->favorite(),
        ];
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        $visibleTypeIds = ActivityType::query()
            ->availableInCRM()
            ->pluck('id')
            ->merge(Activity::query()->whereNotNull('activity_type_id')->distinct()->pluck('activity_type_id'))
            ->map(fn ($id): string => (string) $id)
            ->unique();

        $hiddenTypeIds = ActivityType::query()
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->diff($visibleTypeIds)
            ->values()
            ->all();

        return $kanban
            ->relationshipColumn('activityType', 'name', ActivityType::class, orderAttribute: 'sort', foreignKey: 'activity_type_id')
            ->excludeColumns($hiddenTypeIds)
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['lead.partner', 'activityType', 'assignedTo'])->orderBy('date_deadline'))
            ->cardTitle(fn (Activity $record): string => $record->summary ?: ($record->activityType?->name ?? '—'))
            ->recordUrl(fn (Activity $record): ?string => LeadResourceResolver::viewUrl($record->lead))
            ->cardFooterActions([
                Action::make('done')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.done.label'))
                    ->tooltip(__('crm::filament/clusters/sales/resources/activity.actions.done.label'))
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->color('success')
                    ->action(function (Activity $record): void {
                        if (! ActivityResource::canEdit($record)) {
                            return;
                        }

                        $record->markAsDone();

                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/sales/resources/activity.actions.done.notification.title'))
                            ->body(__('crm::filament/clusters/sales/resources/activity.actions.done.notification.body'))
                            ->send();
                    }),
            ])
            ->columnHeaderAction(
                ScheduleActivityAction::make()
                    ->label('')
                    ->icon('heroicon-o-plus')
            )
            ->columnSummary(function (Collection $records): ?string {
                $overdue = $records->filter(fn (Activity $record): bool => $record->getDueStatus() === ActivityDueStatus::OVERDUE)->count();

                return $overdue > 0 ? __('crm::filament/clusters/sales/resources/activity/pages/kanban-activities.overdue-count', ['count' => $overdue]) : null;
            })
            ->emptyState(
                __('crm::filament/clusters/sales/resources/activity/pages/kanban-activities.empty-state.heading'),
                __('crm::filament/clusters/sales/resources/activity/pages/kanban-activities.empty-state.description'),
                'heroicon-o-clock',
            )
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.activity-card');
    }
}
