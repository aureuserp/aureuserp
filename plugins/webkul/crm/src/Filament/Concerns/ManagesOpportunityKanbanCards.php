<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Enums\KanbanActivityState;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Support\KanbanCard;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\Lead;
use Wezlo\FilamentKanban\KanbanColumn;

trait ManagesOpportunityKanbanCards
{
    public const KANBAN_CARD_RELATIONS = ['partner', 'user', 'tags', 'company.currency', 'recurringPlan', 'crmActivities.activityType'];

    public array $kanbanActivityFilter = [];

    protected bool $ignoreKanbanActivityFilter = false;

    protected ?array $kanbanProgress = null;

    abstract protected function kanbanColumnKeyExpression(): string;

    public function getKanbanCard(Lead $record): KanbanCard
    {
        $key = (string) $record->getKey();
        $resource = LeadResourceResolver::resourceFor($record);
        $canEdit = $resource::canEdit($record);
        $currency = $record->company?->currency?->code ?? 'USD';
        $shows = fn (string $column): bool => ! method_exists($this, 'isKanbanCardFieldVisible') || $this->isKanbanCardFieldVisible($column);

        $activities = $record->crmActivities->sortBy(fn (Activity $activity): int => $activity->date_deadline?->timestamp ?? PHP_INT_MAX)->values();
        $nextActivity = $activities->first();
        $activityState = $record->activityState();
        $activityCount = $activities->count();
        $activitySummary = match ($activityState) {
            ActivityDueStatus::OVERDUE => trans_choice('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities-overdue', $activityCount, ['count' => $activityCount]),
            ActivityDueStatus::TODAY   => trans_choice('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities-today', $activityCount, ['count' => $activityCount]),
            ActivityDueStatus::PLANNED => trans_choice('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities-planned', $activityCount, ['count' => $activityCount]),
            default                    => null,
        };

        $card = KanbanCard::make($record->name)
            ->url($this->getKanbanBoard()->getRecordUrl($record), __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.open', ['name' => $record->name]))
            ->titleTooltip(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.title').': '.$record->name);

        if ($canEdit) {
            $card
                ->menuItem(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.edit'), 'heroicon-m-pencil-square', url: $resource::getUrl('edit', ['record' => $record]))
                ->menuItem(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.color'), 'heroicon-m-swatch', click: "mountAction('kanbanCardColor', { record: '{$key}' })");
        }

        if ($resource::canForceDelete($record)) {
            $card->menuItem(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.delete'), 'heroicon-m-trash', click: "mountAction('deleteKanbanCard', { record: '{$key}' })", color: 'danger');
        }

        if ($shows('expected_revenue')
            && (float) $record->expected_revenue > 0) {
            $card->line(
                Number::currency((float) $record->expected_revenue, $currency),
                tooltip: __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.expected-revenue'),
                strong: true,
                suffix: (float) $record->recurring_revenue > 0 ? '+ '.Number::currency((float) $record->recurring_revenue, $currency).($record->recurringPlan ? ' '.$record->recurringPlan->name : '') : null,
            );
        }

        if ($shows('partner.name')) {
            $card->line($record->partner?->name ?? $record->contact_name ?? $record->company_name, 'heroicon-m-building-office-2', __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.customer'));
        }

        if ($shows('tags.name')) {
            $card->tags($record->tags, __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.tags'));
        }

        return $card
            ->start(
                $shows('priority') ? KanbanCard::stars(
                    $key,
                    $record->priority?->value ?? 0,
                    collect(OpportunityPriority::cases())->filter(fn (OpportunityPriority $priority): bool => $priority->value > 0)->mapWithKeys(fn (OpportunityPriority $priority): array => [$priority->value => $priority->getLabel()])->all(),
                    $canEdit,
                    __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.priority'),
                    $record->priority?->getLabel(),
                ) : null,
                KanbanCard::button(
                    str_starts_with((string) $nextActivity?->activityType?->icon, 'heroicon-') ? $nextActivity->activityType->icon : 'heroicon-o-clock',
                    "mountAction('".($activities->isEmpty() && $canEdit ? 'scheduleKanbanActivity' : 'kanbanCardActivities')."', { record: '{$key}' })",
                    __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities'),
                    $activitySummary
                        ? $activitySummary.($nextActivity?->activityType ? ' · '.$nextActivity->activityType->name : '')
                        : __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.no-activities'),
                    match ($activityState) {
                        ActivityDueStatus::OVERDUE => 'danger',
                        ActivityDueStatus::TODAY   => 'warning',
                        ActivityDueStatus::PLANNED => 'success',
                        default                    => 'gray',
                    },
                ),
            )
            ->end(
                KanbanCard::dot($record->color, __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.color')),
                KanbanCard::status(match (true) {
                    $record->trashed() => __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.lost'),
                    $record->isWon()   => __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.won'),
                    default            => null,
                }, $record->trashed() ? 'danger' : 'success'),
                $shows('user.name') ? KanbanCard::user($record->user, __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.salesperson')) : null,
            );
    }

    public function setKanbanCardPriority(string $recordId, int $priority): void
    {
        $lead = $this->findEditableKanbanCard($recordId);

        if (! $lead
            || ! OpportunityPriority::tryFrom($priority)) {
            return;
        }

        $lead->update(['priority' => $lead->priority?->value === $priority ? OpportunityPriority::LOW : $priority]);
    }

    public function kanbanCardColorAction(): Action
    {
        return Action::make('kanbanCardColor')
            ->record(fn (array $arguments): ?Lead => Lead::query()->find($arguments['record'] ?? null))
            ->modalHeading(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.color'))
            ->modalWidth('sm')
            ->modalFooterActionsAlignment(Alignment::End)
            ->authorize(fn (?Lead $record): bool => $record && LeadResourceResolver::resourceFor($record)::canEdit($record))
            ->fillForm(fn (Lead $record): array => ['color' => $record->color])
            ->schema([
                ColorPicker::make('color')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.color'))
                    ->hexColor(),
            ])
            ->action(fn (array $data, Lead $record): bool => $record->update(['color' => filled($data['color'] ?? null) ? $data['color'] : null]));
    }

    protected function findEditableKanbanCard(string $recordId): ?Lead
    {
        $lead = Lead::query()->find($recordId);

        return $lead && LeadResourceResolver::resourceFor($lead)::canEdit($lead) ? $lead : null;
    }

    public function deleteKanbanCardAction(): Action
    {
        return Action::make('deleteKanbanCard')
            ->record(fn (array $arguments): ?Lead => Lead::query()->find($arguments['record'] ?? null))
            ->requiresConfirmation()
            ->color('danger')
            ->modalHeading(fn (?Lead $record): string => __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.delete-heading', ['name' => $record?->name]))
            ->modalDescription(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.delete-description'))
            ->authorize(fn (?Lead $record): bool => $record && LeadResourceResolver::resourceFor($record)::canForceDelete($record))
            ->action(function (Lead $record): void {
                $record->forceDelete();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.deleted'))
                    ->send();
            });
    }

    public function kanbanCardActivitiesAction(): Action
    {
        return Action::make('kanbanCardActivities')
            ->record(fn (array $arguments): ?Lead => Lead::query()->with(['crmActivities.activityType', 'crmActivities.assignedTo'])->find($arguments['record'] ?? null))
            ->authorize(fn (?Lead $record): bool => $record && LeadResourceResolver::resourceFor($record)::canView($record))
            ->modalHeading(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activities'))
            ->modalDescription(fn (Lead $record): string => $record->name)
            ->modalWidth('2xl')
            ->modalSubmitAction(false)
            ->schema([
                RepeatableEntry::make('crmActivities')
                    ->hiddenLabel()
                    ->state(fn (Lead $record): array => $record->crmActivities->sortBy(fn (Activity $activity): int => $activity->date_deadline?->timestamp ?? PHP_INT_MAX)->values()->all())
                    ->schema([
                        TextEntry::make('activityType.name')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->icon(fn (Activity $record): string => str_starts_with((string) $record->activityType?->icon, 'heroicon-') ? $record->activityType->icon : 'heroicon-o-clock')
                            ->placeholder('—'),
                        TextEntry::make('due_status')
                            ->hiddenLabel()
                            ->state(fn (Activity $record): ActivityDueStatus => $record->getDueStatus())
                            ->badge()
                            ->alignEnd(),
                        TextEntry::make('summary')
                            ->hiddenLabel()
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('date_deadline')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activity-deadline'))
                            ->date()
                            ->placeholder('—'),
                        TextEntry::make('assignedTo.name')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.activity-assigned-to'))
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ])
            ->extraModalFooterActions(fn (Lead $record): array => [
                Action::make('scheduleAnotherKanbanActivity')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.card.schedule-activity'))
                    ->icon('heroicon-o-plus')
                    ->visible(fn (): bool => LeadResourceResolver::resourceFor($record)::canEdit($record))
                    ->action(fn () => $this->replaceMountedAction('scheduleKanbanActivity', ['record' => $record->getKey()])),
            ]);
    }

    public function scheduleKanbanActivityAction(): Action
    {
        return ActivityTableAction::make('scheduleKanbanActivity')
            ->record(fn (array $arguments): ?Lead => Lead::query()->find($arguments['record'] ?? null))
            ->authorize(fn (?Lead $record): bool => $record && LeadResourceResolver::resourceFor($record)::canEdit($record));
    }

    protected function applyKanbanActivityFilter(Builder $query): Builder
    {
        foreach ($this->kanbanActivityFilter as $columnValue => $state) {
            if (! KanbanActivityState::tryFrom((string) $state)) {
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
        if (! KanbanActivityState::tryFrom($state)) {
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

        return $this->kanbanProgress[$column->value] ?? KanbanActivityState::emptyCounts();
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
                $record->overdue_activities > 0 => KanbanActivityState::OVERDUE,
                $record->today_activities > 0   => KanbanActivityState::TODAY,
                $record->planned_activities > 0 => KanbanActivityState::PLANNED,
                default                         => KanbanActivityState::NONE,
            };

            $progress[(string) $record->kanban_column_key] ??= KanbanActivityState::emptyCounts();
            $progress[(string) $record->kanban_column_key][$state->value]++;
        }

        return $progress;
    }
}
