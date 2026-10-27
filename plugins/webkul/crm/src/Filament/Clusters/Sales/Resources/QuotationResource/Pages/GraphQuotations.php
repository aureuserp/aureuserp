<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Enums\QuotationBoardGroup;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;
use Webkul\Sale\Models\Order;

class GraphQuotations extends ListQuotations
{
    use StylesCRMDashboardCharts;

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/quotation.views.graph');
    }

    public function getRecordNavigationTabs(): array
    {
        return QuotationResource::getViewTabs('graph');
    }

    public function getBoardGroup(): ?QuotationBoardGroup
    {
        return null;
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->paginated(false)
            ->emptyState(fn () => view('crm::filament.leads.graph', ['chart' => $this->getRecordsGraph()]));
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return new Collection;
    }

    public function getRecordsGraph(): array
    {
        $query = $this->filterTableQuery($this->modifyQueryWithActiveTab(QuotationResource::getEloquentQuery()))->reorder();
        $grouping = (string) str((string) $this->tableGrouping)->before(':');

        if ($group = QuotationBoardGroup::fromGrouping($grouping)) {
            $groups = $query
                ->with($group->relation() ? [$group->relation()] : [])
                ->get()
                ->groupBy(fn (Order $record): string => $this->boardValue($record, $group));

            $labels = $this->boardLabels($groups, $group);
            $counts = $labels->keys()->map(fn (string|int $value): int => $groups->get((string) $value, new Collection)->count());
        } else {
            $dateColumn = in_array($grouping, ['date_order', 'commitment_date'], true) ? $grouping : 'date_order';

            $months = $query
                ->selectRaw(Sql::month('sales_orders.'.$dateColumn).' as graph_month, count(*) as aggregate')
                ->groupBy('graph_month')
                ->orderBy('graph_month')
                ->pluck('aggregate', 'graph_month');

            $labels = $months->keys()->map(fn (?string $month): string => filled($month) ? Dimension::monthLabel($month) : Dimension::getNoneLabel());
            $counts = $months->values()->map(fn (mixed $count): int => (int) $count);
        }

        return [
            'data' => [
                'datasets' => [$this->barDataset(QuotationResource::getPluralModelLabel(), $counts->values()->all())],
                'labels'   => $labels->values()->all(),
            ],
            'options' => $this->barOptions(),
        ];
    }

    protected function getTableViewsFilterableType(): string
    {
        return ListQuotations::class;
    }
}
