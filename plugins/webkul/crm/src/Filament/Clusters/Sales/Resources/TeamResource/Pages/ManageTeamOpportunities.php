<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Attributes\Url;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Enums\PipelineView;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\GraphOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Tables\OpportunitiesTable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\ManagesOpportunityKanbanCards;
use Webkul\Crm\Filament\Concerns\ShowsLeadsGraph;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\TableViews\Filament\Components\PresetView;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;

class ManageTeamOpportunities extends ManageRelatedRecords
{
    use HasCRMClusterNavigation, HasCRMTableViews, HasKanbanBoard, ManagesOpportunityKanbanCards, ShowsLeadsGraph {
        HasKanbanBoard::moveRecord as moveBaseRecord;
    }

    protected static string $resource = TeamResource::class;

    protected static string $relationship = 'opportunities';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    #[Url(as: 'view')]
    public string $pipelineView = PipelineView::KANBAN->value;

    public static function canAccess(array $parameters = []): bool
    {
        $team = $parameters['record'] ?? null;

        return parent::canAccess($parameters)
            && ($team instanceof Model ? TeamResource::canView($team) : TeamResource::canViewAny());
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.navigation.title');
    }

    public function mount(int|string $record): void
    {
        $this->pipelineView = $this->getPipelineView()->value;

        parent::mount($record);
    }

    public function getPipelineView(): PipelineView
    {
        return PipelineView::tryFrom($this->pipelineView) ?? PipelineView::KANBAN;
    }

    public function content(Schema $schema): Schema
    {
        return parent::content($schema);
    }

    public function getTitle(): string|Htmlable
    {
        return __('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.title', ['team' => $this->getRecord()->name]);
    }

    public function getBreadcrumb(): string
    {
        return $this->getPipelineView()->getLabel();
    }

    public function getRecordNavigationTabs(): array
    {
        return array_map(
            fn (PipelineView $view): NavigationItem => NavigationItem::make($view->value)
                ->label($view->getLabel())
                ->icon($view->getIcon())
                ->url(TeamResource::getUrl('opportunities', [
                    'record' => $this->getRecord(),
                    ...($view === PipelineView::KANBAN ? [] : ['view' => $view->value]),
                ]))
                ->isActiveWhen(fn (): bool => $this->getPipelineView() === $view),
            PipelineView::cases(),
        );
    }

    public function form(Schema $schema): Schema
    {
        return OpportunityForm::configure($schema, withinTeam: true);
    }

    public function table(Table $table): Table
    {
        $table = OpportunitiesTable::configure($table, withinTeam: true)
            ->modifyQueryUsing(fn ($query) => $query->opportunities())
            ->modelLabel(OpportunityResource::getModelLabel())
            ->pluralModelLabel(OpportunityResource::getPluralModelLabel())
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActivityTableAction::make()
                    ->tooltip(__('crm::filament/clusters/sales/resources/opportunity.table.actions.schedule-activity.label'))
                    ->visible(fn (Lead $record): bool => OpportunityResource::canEdit($record)),
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record])),
                    EditAction::make()
                        ->url(fn (Lead $record): string => OpportunityResource::getUrl('edit', ['record' => $record])),
                    MarkAsWonAction::make(),
                    MarkAsLostAction::make(),
                ]),
            ]);

        return match ($this->getPipelineView()) {
            PipelineView::KANBAN => $table
                ->paginated(false)
                ->groups([])
                ->emptyState(fn () => view($this->getKanbanBoard()->getBoardView())),
            PipelineView::GRAPH => $table
                ->paginated(false)
                ->emptyState(fn () => view('crm::filament.leads.graph', ['chart' => $this->getRecordsGraph()])),
            PipelineView::LIST => $table,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->model(Lead::class)
                ->relationship(fn (): HasMany => $this->getRecord()->opportunities())
                ->schema(fn (Schema $schema): Schema => OpportunityForm::configure($schema, withinTeam: true))
                ->visible(fn (): bool => OpportunityResource::canCreate())
                ->modalWidth('6xl')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.notification.title'))
                        ->body(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->getPipelineView() === PipelineView::LIST ? parent::getTableRecords() : new Collection;
    }

    protected function getTeamOpportunitiesQuery(): Builder
    {
        return OpportunityResource::getEloquentQuery()
            ->opportunities()
            ->where('crm_leads.team_id', $this->getRecord()->getKey());
    }

    public function getRecordsGraph(): array
    {
        $group = GraphOpportunities::GRAPH_GROUPS[(string) str((string) $this->tableGrouping)->before(':')] ?? GraphOpportunities::GRAPH_GROUPS['stage.name'];

        return $this->buildLeadsGraph(
            $this->filterTableQuery($this->modifyQueryWithActiveTab($this->getTeamOpportunitiesQuery())),
            $group,
            OpportunityResource::getPluralModelLabel(),
        );
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        $query = $this->filterTableQuery($this->modifyQueryWithActiveTab($this->getTeamOpportunitiesQuery()))
            ->with(static::KANBAN_CARD_RELATIONS);

        return $this->ignoreKanbanActivityFilter ? $query : $this->applyKanbanActivityFilter($query);
    }

    protected function kanbanColumnKeyExpression(): string
    {
        return Sql::text('crm_leads.stage_id');
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        return $kanban
            ->relationshipColumn('stage', 'name', Stage::class, orderAttribute: 'sort')
            ->excludeColumns(Stage::hiddenColumnsForTeam($this->getRecord()->getKey()))
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
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
        $record = $this->getTeamOpportunitiesQuery()->withoutTrashed()->find($recordId);

        if (! $record
            || ! OpportunityResource::canEdit($record)) {
            Notification::make()
                ->warning()
                ->title($record
                    ? __('crm::filament/clusters/leads/resources/lead/pages/list-leads.board.notifications.unauthorized')
                    : __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.lost-move'))
                ->send();

            return;
        }

        $this->moveBaseRecord($recordId, $newColumnValue, $orderedIds);
    }

    public function getPresetTableViews(): array
    {
        return array_map(
            fn (PresetView $view): PresetView => $view->setAsDefault(false),
            (new ListOpportunities)->getPresetTableViews(),
        );
    }

    protected function getTableViewCountQuery(): Builder
    {
        return OpportunityResource::getEloquentQuery()->where('team_id', $this->getRecord()->getKey());
    }
}
