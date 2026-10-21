<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Concerns\ManagesActivityTypeKanbanColumns;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Activity;
use Webkul\Support\Models\ActivityType;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;

class KanbanActivities extends ListRecords
{
    use HasKanbanBoard, ManagesActivityTypeKanbanColumns {
        kanbanColumnAction as kanbanColumnActionFromBoard;
    }

    protected static string $resource = ActivityResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->resetKanbanFilters();

        $this->kanbanFilters['assigned_to'] = Auth::id();
    }

    public function resetKanbanFilters(): void
    {
        $this->kanbanFilters = [
            'assigned_to' => null,
        ];
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
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->orderBy('date_deadline'))
            ->cardTitle(fn (Activity $record): string => $record->lead?->name ?? '—')
            ->cardDescription(fn (Activity $record): string => collect([
                $record->summary,
                $record->date_deadline?->translatedFormat('M j, Y'),
            ])->filter()->implode(' · '))
            ->cardBadges(fn (Activity $record): array => array_values(array_filter([
                ['label' => $record->getDueStatus()->getLabel(), 'color' => $record->getDueStatus()->getColor()],
                $record->assignedTo ? ['label' => $record->assignedTo->name, 'color' => 'gray'] : null,
                $record->lead?->partner ? ['label' => $record->lead->partner->name, 'color' => 'info'] : null,
            ])))
            ->recordUrl(fn (Activity $record): ?string => LeadResourceResolver::viewUrl($record->lead))
            ->cardFooterActions([
                Action::make('open')
                    ->label(__('crm::filament/clusters/sales/resources/activity.actions.open.label'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Activity $record): ?string => LeadResourceResolver::viewUrl($record->lead)),
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
            ->cardView('crm::filament.kanban.card');
    }
}
