<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Actions\CRMExportAction;
use Webkul\Crm\Filament\Actions\CRMImportAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Concerns\HasKanbanTableViews;
use Webkul\Crm\Filament\Concerns\ManagesOpportunityKanbanCards;
use Webkul\Crm\Filament\Concerns\ManagesStageKanbanColumns;
use Webkul\Crm\Filament\Exports\OpportunityExporter;
use Webkul\Crm\Filament\Imports\OpportunityImporter;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Support\Models\Company;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class KanbanOpportunities extends ListRecords
{
    use HasKanbanBoard, HasKanbanTableViews, ManagesOpportunityKanbanCards, ManagesStageKanbanColumns {
        HasKanbanTableViews::content insteadof HasKanbanBoard;
        HasKanbanTableViews::getKanbanQuery insteadof HasKanbanBoard;
        HasKanbanBoard::moveRecord as moveBaseRecord;
    }

    protected static string $resource = OpportunityResource::class;

    protected function getTableViewsListPage(): ListRecords
    {
        return new ListOpportunities;
    }

    public function mount(): void
    {
        parent::mount();

        if ($teamId = request()->integer('team')) {
            $this->tableFilters = [
                'queryBuilder' => [
                    'rules' => [
                        'team' => ['type' => 'team', 'data' => ['operator' => 'isRelatedTo', 'settings' => ['values' => [(string) $teamId]]]],
                    ],
                ],
            ];
        }
    }

    protected function modifyKanbanQuery(Builder $query): Builder
    {
        $query->withGlobalScope(SoftDeletingScope::class, new SoftDeletingScope);

        return $this->ignoreKanbanActivityFilter ? $query : $this->applyKanbanActivityFilter($query);
    }

    protected function kanbanTeamFilterId(): ?int
    {
        $teamIds = collect(data_get($this->tableFilters, 'queryBuilder.rules', []))
            ->filter(fn (mixed $rule): bool => data_get($rule, 'type') === 'team' && data_get($rule, 'data.operator') === 'isRelatedTo')
            ->flatMap(fn (array $rule): array => (array) data_get($rule, 'data.settings.values', []))
            ->filter()
            ->unique();

        return $teamIds->count() === 1 ? (int) $teamIds->first() : null;
    }

    protected function kanbanColumnKeyExpression(): string
    {
        return 'crm_leads.stage_id';
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.title');
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.breadcrumb');
    }

    public function getRecordNavigationTabs(): array
    {
        return OpportunityResource::getViewTabs('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            CRMImportAction::make()
                ->importer(OpportunityImporter::class)
                ->visible(fn (): bool => OpportunityResource::canCreate()),
            CRMExportAction::make()
                ->exporter(OpportunityExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => CRMExportAction::limitToVisibleRecords(
                    $query->whereIn('crm_leads.id', $this->getKanbanQuery($this->getKanbanBoard())->reorder()->pluck('crm_leads.id')),
                )),
            Action::make('create')
                ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(fn (): string => OpportunityResource::getUrl('create'))
                ->visible(fn (): bool => OpportunityResource::canCreate()),
        ];
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        $wonStageIds = Stage::query()->where('is_won', true)->pluck('id')->map(fn ($id): string => (string) $id)->all();

        $currencyCode = Company::query()->find(current_company_id())?->currency?->code ?? 'USD';

        return $kanban
            ->relationshipColumn('stage', 'name', Stage::class, orderAttribute: 'sort')
            ->excludeColumns(Stage::hiddenColumnsForTeam($this->kanbanTeamFilterId()))
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(static::KANBAN_CARD_RELATIONS))
            ->columnColor(fn (KanbanColumn $column): string => in_array($column->value, $wonStageIds, true) ? 'success' : 'gray')
            ->cardTitle(fn (Lead $record): string => $record->name)
            ->cardDescription(fn (Lead $record): string => collect([
                $record->partner?->name,
                (float) $record->expected_revenue > 0
                    ? Number::currency((float) $record->expected_revenue, $record->company?->currency?->code ?? $currencyCode)
                    : null,
                RecurringPlanResource::isRecurringRevenueEnabled() && (float) $record->recurring_revenue > 0
                    ? '+ '.Number::currency((float) $record->recurring_revenue, $record->company?->currency?->code ?? $currencyCode).($record->recurringPlan ? ' '.$record->recurringPlan->name : '')
                    : null,
            ])->filter()->implode(' · '))
            ->cardBadges(fn (Lead $record): array => collect([
                $record->priority && $record->priority !== OpportunityPriority::LOW
                    ? ['label' => $record->priority->getLabel(), 'color' => $record->priority->getColor()]
                    : null,
                $record->user ? ['label' => $record->user->name, 'color' => 'gray'] : null,
                ...$record->tags->map(fn ($tag): array => ['label' => $tag->name, 'color' => 'info'])->all(),
            ])->filter()->values()->all())
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
            ->columnSummary(function (Collection $records) use ($currencyCode): ?string {
                $total = (float) $records->sum('expected_revenue');

                return $total > 0 ? Number::currency($total, $currencyCode) : null;
            })
            ->columnHeaderAction(
                Action::make('quickCreate')
                    ->icon('heroicon-o-plus')
                    ->modalHeading(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.quick-create.heading'))
                    ->modalWidth('lg')
                    ->visible(fn (): bool => OpportunityResource::canCreate())
                    ->schema([
                        Hidden::make('stage_id'),
                        TextInput::make('name')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.name'))
                            ->placeholder(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.name-placeholder'))
                            ->required()
                            ->maxLength(255),
                        Select::make('partner_id')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.customer'))
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('expected_revenue')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.expected-revenue'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        Select::make('priority')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.priority'))
                            ->options(OpportunityPriority::class)
                            ->default(OpportunityPriority::LOW),
                    ])
                    ->action(function (array $data): void {
                        Lead::create([
                            ...$data,
                            'stage_id'   => $this->validatedQuickCreateStageId($data['stage_id'] ?? null),
                            'team_id'    => $this->currentKanbanTeamId(),
                            'company_id' => current_company_id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/create-opportunity.notification.title'))
                            ->body(__('crm::filament/clusters/sales/resources/opportunity/pages/create-opportunity.notification.body'))
                            ->send();
                    })
            )
            ->emptyState(
                __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.empty-state.heading'),
                __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.empty-state.description'),
                'heroicon-o-funnel',
            )
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.opportunity-card');
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        if ($this->activeTableView === 'lost') {
            Notification::make()
                ->warning()
                ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.lost-move'))
                ->send();

            return;
        }

        $this->moveBaseRecord($recordId, $newColumnValue, $orderedIds);
    }

    protected function validatedQuickCreateStageId(mixed $stageId): ?int
    {
        return Stage::query()->whereKey($stageId)->value('id')
            ?? Stage::query()->orderBy('sort')->value('id');
    }
}
