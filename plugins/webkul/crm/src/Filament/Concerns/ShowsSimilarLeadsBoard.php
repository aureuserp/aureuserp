<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\BulkActionGroup;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;
use Webkul\Crm\Enums\ViewLayout;
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
    use HasCRMTableViews, HasKanbanBoard, HasRecordNavigationTabs, HasViewLayouts, InteractsWithRecord, ManagesOpportunityKanbanCards {
        HasKanbanBoard::getKanbanColumns as getAllKanbanColumns;
        HasRecordNavigationTabs::getSubNavigation insteadof InteractsWithRecord;
    }

    abstract protected function getTableViewsListPage(): ListRecords;

    abstract protected function configureSimilarTable(Table $table): Table;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

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

    public function getViewLayouts(): array
    {
        return [ViewLayout::KANBAN, ViewLayout::LIST, ViewLayout::GRAPH];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        $table = $this->configureSimilarTable($table)
            ->query(fn (): Builder => $this->modifyQueryWithActiveTab($this->getSimilarQuery()))
            ->recordUrl(fn (Lead $record): ?string => LeadResourceResolver::viewUrl($record))
            ->recordActions([])
            ->toolbarActions($this->isViewLayout(ViewLayout::LIST) ? [
                BulkActionGroup::make([
                    MergeLeadsBulkAction::make(),
                ]),
            ] : []);

        if ($this->isViewLayout(ViewLayout::GRAPH)) {
            return $table
                ->paginated(false)
                ->emptyState(fn (): HtmlString => new HtmlString('<div class="crm-leads-graph">'.Livewire::mount(SimilarLeadsChart::class, ['leadId' => $this->getRecord()->getKey()], 'crm-similar-leads-chart-'.$this->getRecord()->getKey()).'</div>'));
        }

        if (! $this->isViewLayout(ViewLayout::KANBAN)) {
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
        return $this->isViewLayout(ViewLayout::LIST) ? parent::getTableRecords() : new EloquentCollection;
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
            ->boardView('crm::filament.kanban.board');
    }
}
