<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Models\Lead;
use Wezlo\FilamentKanban\KanbanColumn;

trait ManagesOpportunityKanbanCards
{
    public const KANBAN_CARD_RELATIONS = ['partner', 'user', 'tags', 'company.currency', 'recurringPlan', 'crmActivities.activityType'];

    public const KANBAN_ACTIVITY_STATES = ['planned', 'today', 'overdue', 'none'];

    public array $kanbanActivityFilter = [];

    protected bool $ignoreKanbanActivityFilter = false;

    protected ?array $kanbanProgress = null;

    abstract protected function kanbanColumnKeyExpression(): string;

    public function setKanbanCardPriority(string $recordId, int $priority): void
    {
        $lead = $this->findEditableKanbanCard($recordId);

        if (! $lead || ! OpportunityPriority::tryFrom($priority)) {
            return;
        }

        $lead->update(['priority' => $lead->priority?->value === $priority ? OpportunityPriority::Low : $priority]);
    }

    public function setKanbanCardColor(string $recordId, int $color): void
    {
        $lead = $this->findEditableKanbanCard($recordId);

        if (! $lead || ($color !== 0 && ! array_key_exists($color, Lead::COLORS))) {
            return;
        }

        $lead->update(['color' => $color]);
    }

    protected function findEditableKanbanCard(string $recordId): ?Lead
    {
        $lead = Lead::query()->find($recordId);

        return $lead && OpportunityResource::canEdit($lead) ? $lead : null;
    }

    public function deleteKanbanCardAction(): Action
    {
        return Action::make('deleteKanbanCard')
            ->record(fn (array $arguments): ?Lead => Lead::query()->find($arguments['record'] ?? null))
            ->requiresConfirmation()
            ->color('danger')
            ->modalHeading(fn (?Lead $record): string => __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.delete-heading', ['name' => $record?->name]))
            ->modalDescription(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.delete-description'))
            ->authorize(fn (?Lead $record): bool => $record && OpportunityResource::canForceDelete($record))
            ->action(function (Lead $record): void {
                $record->forceDelete();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.deleted'))
                    ->send();
            });
    }

    public function scheduleKanbanActivityAction(): Action
    {
        return ActivityTableAction::make('scheduleKanbanActivity')
            ->record(fn (array $arguments): ?Lead => Lead::query()->find($arguments['record'] ?? null))
            ->authorize(fn (?Lead $record): bool => $record && OpportunityResource::canEdit($record));
    }

    protected function applyKanbanActivityFilter(Builder $query): Builder
    {
        foreach ($this->kanbanActivityFilter as $columnValue => $state) {
            if (! in_array($state, static::KANBAN_ACTIVITY_STATES, true)) {
                continue;
            }

            $query->where(fn (Builder $query): Builder => $query
                ->whereRaw("COALESCE({$this->kanbanColumnKeyExpression()}, 'none') != ?", [$columnValue])
                ->orWhere(fn (Builder $query): Builder => $query->activityState($state)));
        }

        return $query;
    }

    public function toggleKanbanActivityFilter(string $columnValue, string $state): void
    {
        if (! in_array($state, static::KANBAN_ACTIVITY_STATES, true)) {
            return;
        }

        if (($this->kanbanActivityFilter[$columnValue] ?? null) === $state) {
            unset($this->kanbanActivityFilter[$columnValue]);

            return;
        }

        $this->kanbanActivityFilter[$columnValue] = $state;
    }

    public function kanbanColumnProgress(KanbanColumn $column): array
    {
        $this->kanbanProgress ??= $this->computeKanbanProgress();

        return $this->kanbanProgress[$column->value] ?? array_fill_keys(static::KANBAN_ACTIVITY_STATES, 0);
    }

    protected function computeKanbanProgress(): array
    {
        $this->ignoreKanbanActivityFilter = true;

        try {
            $records = $this->getKanbanQuery($this->getKanbanBoard())
                ->reorder()
                ->setEagerLoads([])
                ->select('crm_leads.id')
                ->selectRaw("COALESCE({$this->kanbanColumnKeyExpression()}, 'none') as kanban_column_key")
                ->withCount([
                    'crmActivities as overdue_activities' => fn (Builder $query): Builder => $query->overdue(),
                    'crmActivities as today_activities'   => fn (Builder $query): Builder => $query->dueToday(),
                    'crmActivities as planned_activities' => fn (Builder $query): Builder => $query->planned(),
                ])
                ->get();
        } finally {
            $this->ignoreKanbanActivityFilter = false;
        }

        $progress = [];

        foreach ($records as $record) {
            $state = match (true) {
                $record->overdue_activities > 0 => 'overdue',
                $record->today_activities > 0   => 'today',
                $record->planned_activities > 0 => 'planned',
                default                         => 'none',
            };

            $progress[(string) $record->kanban_column_key] ??= array_fill_keys(static::KANBAN_ACTIVITY_STATES, 0);
            $progress[(string) $record->kanban_column_key][$state]++;
        }

        return $progress;
    }
}
