<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\BulkActionGroup;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Webkul\Crm\Filament\Actions\MergeLeadsBulkAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Widgets\SimilarLeadsChart;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Filament\Support\SimilarLeads;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TableViews\Filament\Components\PresetView;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

trait ShowsSimilarLeadsBoard
{
    use HasCRMTableViews, HasKanbanBoard, HasRecordNavigationTabs, InteractsWithRecord, ManagesOpportunityKanbanCards {
        HasKanbanBoard::getKanbanColumns as getAllKanbanColumns;
        HasRecordNavigationTabs::getSubNavigation insteadof InteractsWithRecord;
    }

    #[Url(as: 'view')]
    public string $similarView = 'kanban';

    abstract protected function getTableViewsListPage(): ListRecords;

    abstract protected function configureSimilarTable(Table $table): Table;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        if (! in_array($this->similarView, ['kanban', 'list', 'graph'], true)) {
            $this->similarView = 'kanban';
        }

        $this->kanbanFilters = [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('crm::filament/similar-leads.page.title', ['name' => $this->getRecord()->name]);
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/similar-leads.navigation.label');
    }

    public function getSimilarGroup(): Collection
    {
        /** @var Lead $record */
        $record = $this->getRecord();

        $record->loadMissing(['stage', 'user', 'partner', 'tags', 'company.currency']);

        return new EloquentCollection([$record, ...SimilarLeads::visibleRecords($record)]);
    }

    protected function getSimilarQuery(): Builder
    {
        return Lead::query()->withTrashed()->whereKey($this->getSimilarGroup()->modelKeys());
    }

    public function getSimilarViewTabs(): array
    {
        $tab = fn (string $view, string $label, string $icon): NavigationItem => NavigationItem::make($view)
            ->label($label)
            ->icon($icon)
            ->url(static::getResource()::getUrl('similar', ['record' => $this->getRecord(), 'view' => $view]))
            ->isActiveWhen(fn (): bool => $this->similarView === $view);

        return [
            $tab('kanban', __('crm::filament/similar-leads.page.views.kanban'), 'heroicon-o-view-columns'),
            $tab('list', __('crm::filament/similar-leads.page.views.list'), 'heroicon-o-list-bullet'),
            $tab('graph', __('crm::filament/similar-leads.page.views.graph'), 'heroicon-o-chart-bar'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            View::make('crm::filament.similar-leads.view-tabs')
                ->viewData(['navigationItems' => $this->getSimilarViewTabs()]),
            ...($this->similarView === 'graph'
                ? [Livewire::make(SimilarLeadsChart::class, ['leadId' => $this->getRecord()->getKey()])]
                : [RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE), EmbeddedTable::make()]),
        ]);
    }

    public function table(Table $table): Table
    {
        $table = $this->configureSimilarTable($table)
            ->query(fn (): Builder => $this->modifyQueryWithActiveTab($this->getSimilarQuery()))
            ->recordUrl(fn (Lead $record): ?string => LeadResourceResolver::viewUrl($record))
            ->recordActions([])
            ->toolbarActions($this->similarView === 'list' ? [
                BulkActionGroup::make([
                    MergeLeadsBulkAction::make(),
                ]),
            ] : []);

        if ($this->similarView !== 'kanban') {
            return $table;
        }

        foreach ($table->getColumns() as $column) {
            if (! $column->isToggleable()) {
                $column->toggleable();
            }
        }

        return $table
            ->paginated(false)
            ->groups([])
            ->emptyState(fn () => view($this->getKanbanBoard()->getBoardView()));
    }

    public function getTableRecords(): EloquentCollection|Paginator|CursorPaginator
    {
        return $this->similarView === 'kanban' ? new EloquentCollection : parent::getTableRecords();
    }

    public function isKanbanCardFieldVisible(string $column): bool
    {
        return ! $this->getTable()->getColumn($column)?->isToggledHidden();
    }

    public function getPresetTableViews(): array
    {
        return array_map(
            fn (PresetView $view): PresetView => $view->setAsDefault(false),
            $this->getTableViewsListPage()->getPresetTableViews(),
        );
    }

    public function getSavedTableViews(): array
    {
        return $this->getTableViewsListPage()->getSavedTableViews();
    }

    protected function getTableViewsFilterableType(): string
    {
        return $this->getTableViewsListPage()::class;
    }

    protected function getTableViewCountQuery(): Builder
    {
        return $this->getSimilarQuery();
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        $query = $this->filterTableQuery($this->modifyQueryWithActiveTab($this->getSimilarQuery()))
            ->with(static::KANBAN_CARD_RELATIONS);

        return $this->ignoreKanbanActivityFilter ? $query : $this->applyKanbanActivityFilter($query);
    }

    protected function kanbanColumnKeyExpression(): string
    {
        return Sql::text('crm_leads.stage_id');
    }

    public function getKanbanColumns(): array
    {
        return array_values(array_filter($this->getAllKanbanColumns(), fn (KanbanColumn $column): bool => $column->count > 0));
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void {}

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        return $kanban
            ->relationshipColumn('stage', 'name', Stage::class, orderAttribute: 'sort')
            ->canMove(fn (): bool => false)
            ->recordUrl(fn (Lead $lead): ?string => LeadResourceResolver::viewUrl($lead))
            ->emptyState(
                __('crm::filament/similar-leads.page.empty.heading'),
                __('crm::filament/similar-leads.page.empty.description'),
                'heroicon-o-user-group',
            )
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.opportunity-card');
    }
}
