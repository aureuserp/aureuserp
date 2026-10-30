<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Actions\CRMExportAction;
use Webkul\Crm\Filament\Actions\CRMImportAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasKanbanLayout;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Concerns\ManagesOpportunityKanbanCards;
use Webkul\Crm\Filament\Concerns\ManagesStageKanbanColumns;
use Webkul\Crm\Filament\Concerns\ShowsRecordsGraph;
use Webkul\Crm\Filament\Exports\OpportunityExporter;
use Webkul\Crm\Filament\Imports\OpportunityImporter;
use Webkul\Crm\Filament\Widgets\RecordsChart;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Support\Models\Company;
use Webkul\TableViews\Filament\Components\PresetView;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class ListOpportunities extends ListRecords
{
    use HasCRMTableViews, HasKanbanBoard, HasKanbanLayout, HasViewLayouts, ManagesOpportunityKanbanCards, ManagesStageKanbanColumns, ShowsRecordsGraph {
        HasKanbanLayout::content insteadof HasKanbanBoard;
        HasKanbanLayout::getKanbanQuery insteadof HasKanbanBoard;
        HasKanbanLayout::getTableViewCountQuery insteadof HasCRMTableViews;
        HasKanbanBoard::moveRecord as moveBaseRecord;
    }

    public const GRAPH_GROUPS = [
        'stage.name'            => ['field' => 'stage_id', 'relation' => 'stage', 'order' => 'sort'],
        'team.name'             => ['field' => 'team_id', 'relation' => 'team'],
        'user.name'             => ['field' => 'user_id', 'relation' => 'user'],
        'priority'              => ['field' => 'priority'],
        'expected_closing_date' => ['month' => 'expected_closing_date'],
    ];

    protected static string $resource = OpportunityResource::class;

    public function mount(): void
    {
        parent::mount();

        if ($teamId = request()->integer('team')) {
            $this->activeTableView ??= 'default';

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

    public function getViewLayouts(): array
    {
        return [ViewLayout::KANBAN, ViewLayout::LIST, ViewLayout::GRAPH];
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.title');
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
        $group = self::GRAPH_GROUPS[(string) str((string) $this->tableGrouping)->before(':')] ?? self::GRAPH_GROUPS['stage.name'];

        return $this->buildRecordsGraph(
            $this->filterTableQuery($this->modifyQueryWithActiveTab(OpportunityResource::getEloquentQuery())),
            $group,
            OpportunityResource::getPluralModelLabel(),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            CRMImportAction::make()
                ->importer(OpportunityImporter::class)
                ->visible(fn (): bool => OpportunityResource::canCreate()),
            CRMExportAction::make()
                ->exporter(OpportunityExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => $this->isViewLayout(ViewLayout::KANBAN)
                    ? CRMExportAction::limitToVisibleRecords($query->whereIn('crm_leads.id', $this->getKanbanQuery($this->getKanbanBoard())->reorder()->pluck('crm_leads.id')))
                    : $query),
            Action::make('create')
                ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(fn (): string => OpportunityResource::getUrl('create'))
                ->visible(fn (): bool => OpportunityResource::canCreate()),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_pipeline' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.my-pipeline'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->where('user_id', Auth::id())),

            'open' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.open'))
                ->icon('heroicon-s-funnel')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->open()),

            'unassigned' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.unassigned'))
                ->icon('heroicon-s-user-minus')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->whereNull('user_id')),

            'won' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.won'))
                ->icon('heroicon-s-trophy')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->won()),

            'lost' => PresetView::make(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.lost'))
                ->icon('heroicon-s-hand-thumb-down')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
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
