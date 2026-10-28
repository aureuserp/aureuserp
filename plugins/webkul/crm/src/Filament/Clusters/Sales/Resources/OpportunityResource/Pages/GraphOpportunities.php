<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Concerns\ShowsLeadsGraph;
use Webkul\Crm\Filament\Widgets\RecordsChart;

class GraphOpportunities extends ListOpportunities
{
    use ShowsLeadsGraph;

    public const GRAPH_GROUPS = [
        'stage.name'            => ['field' => 'stage_id', 'relation' => 'stage', 'order' => 'sort'],
        'team.name'             => ['field' => 'team_id', 'relation' => 'team'],
        'user.name'             => ['field' => 'user_id', 'relation' => 'user'],
        'priority'              => ['field' => 'priority'],
        'expected_closing_date' => ['month' => 'expected_closing_date'],
    ];

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity.views.graph');
    }

    public function getRecordNavigationTabs(): array
    {
        return OpportunityResource::getViewTabs('graph');
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->paginated(false)
            ->emptyState(fn () => RecordsChart::embedGraph($this->getRecordsGraph()));
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return new Collection;
    }

    public function getRecordsGraph(): array
    {
        $group = self::GRAPH_GROUPS[(string) str((string) $this->tableGrouping)->before(':')] ?? self::GRAPH_GROUPS['stage.name'];

        return $this->buildLeadsGraph(
            $this->filterTableQuery($this->modifyQueryWithActiveTab(OpportunityResource::getEloquentQuery())),
            $group,
            OpportunityResource::getPluralModelLabel(),
        );
    }

    protected function getTableViewsFilterableType(): string
    {
        return ListOpportunities::class;
    }
}
