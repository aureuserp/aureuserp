<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Concerns\AppliesKanbanListFilters;
use Webkul\Crm\Filament\Concerns\ManagesActivityTypeKanbanColumns;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Activity;
use Webkul\Support\Models\ActivityType;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class KanbanActivities extends ListRecords
{
    use AppliesKanbanListFilters, HasKanbanBoard, ManagesActivityTypeKanbanColumns {
        AppliesKanbanListFilters::getKanbanQuery insteadof HasKanbanBoard;
        HasKanbanBoard::getKanbanQuery as getBaseKanbanQuery;
        kanbanColumnAction as kanbanColumnActionFromBoard;
    }

    protected static string $resource = ActivityResource::class;

    public array $kanbanActivityFilter = [];

    protected bool $ignoreKanbanActivityFilter = false;

    protected ?array $kanbanProgress = null;

    public function mount(): void
    {
        parent::mount();

        $this->resetKanbanFilters();
    }

    public function resetKanbanFilters(): void
    {
        $this->kanbanFilters = static::emptyKanbanFilters();
        $this->kanbanActivityFilter = [];
    }

    public static function emptyKanbanFilters(): array
    {
        return [
            'assigned_to'      => null,
            'due_status'       => null,
            'activity_type_id' => [],
        ];
    }

    protected function kanbanSimpleFilterKeys(): array
    {
        return ['assigned_to'];
    }

    protected function applyKanbanListFilters(Builder $query, array $filters): Builder
    {
        $dueStatus = ActivityDueStatus::tryFrom((string) ($filters['due_status'] ?? ''));
        $typeIds = static::kanbanFilterValues($filters, 'activity_type_id');

        $query
            ->when($dueStatus, fn (Builder $query): Builder => $query->dueStatus($dueStatus))
            ->when($typeIds, fn (Builder $query): Builder => $query->whereIn('activity_type_id', $typeIds));

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
        return __('crm::filament/clusters/sales/resources/activity.views.kanban');
    }

    protected function getHeaderActions(): array
    {
        return ActivityResource::getViewSwitcherActions('index');
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        $visibleTypeIds = ActivityType::query()
            ->where('plugin', 'crm')
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
                $overdue = $records->filter(fn (Activity $record): bool => $record->getDueStatus() === ActivityDueStatus::Overdue)->count();

                return $overdue > 0 ? __('crm::filament/clusters/sales/resources/activity/pages/kanban-activities.overdue-count', ['count' => $overdue]) : null;
            })
            ->searchable(['summary', 'lead.name'])
            ->filters([
                Select::make('assigned_to')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.filters.assigned-to'))
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('crm::filament/clusters/sales/resources/activity/pages/kanban-activities.filters.everyone')),
                Select::make('due_status')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.filters.due-status'))
                    ->options(ActivityDueStatus::class)
                    ->placeholder(__('crm::filament/clusters/sales/resources/activity/pages/kanban-activities.filters.all')),
                Select::make('activity_type_id')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.filters.activity-type'))
                    ->options(fn (): array => ActivityType::query()->where('plugin', 'crm')->orderBy('name')->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->placeholder(__('crm::filament/clusters/sales/resources/activity/pages/kanban-activities.filters.all')),
            ])
            ->filtersColumns(1)
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
