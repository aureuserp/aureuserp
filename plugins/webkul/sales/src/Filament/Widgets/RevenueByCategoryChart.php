<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class RevenueByCategoryChart extends DashboardChartWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected const TOP_SLICES = 3;

    protected static ?int $sort = 3;

    /**
     * @var array{labels: list<string>, data: list<float>}|null
     */
    protected ?array $resolvedData = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.revenue-by-category.heading');
    }

    /**
     * @return array{labels: list<string>, data: list<float>}
     */
    protected function resolveChartData(): array
    {
        if ($this->resolvedData !== null) {
            return $this->resolvedData;
        }

        $revenue = 'SUM(CASE WHEN sales_orders.currency_rate > 0 THEN sales_order_lines.price_total / sales_orders.currency_rate ELSE sales_order_lines.price_total END)';

        $rows = $this->confirmedLines()
            ->leftJoin('products_products', 'products_products.id', '=', 'sales_order_lines.product_id')
            ->leftJoin('products_categories', 'products_categories.id', '=', 'products_products.category_id')
            ->leftJoin('sales_orders', 'sales_orders.id', '=', 'sales_order_lines.order_id')
            ->groupBy('products_categories.id', 'products_categories.name')
            ->addSelect('products_categories.name as name')
            ->selectRaw("{$revenue} as revenue")
            ->havingRaw("{$revenue} > 0")
            ->orderByDesc('revenue')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows->take(self::TOP_SLICES) as $row) {
            $labels[] = $row->name ?: __('sales::filament/widgets/sales-dashboard.unknown');
            $data[] = round((float) $row->revenue, 2);
        }

        $other = round((float) $rows->skip(self::TOP_SLICES)->sum('revenue'), 2);

        if ($other > 0) {
            $labels[] = __('sales::filament/widgets/sales-dashboard.other');
            $data[] = $other;
        }

        return $this->resolvedData = ['labels' => $labels, 'data' => $data];
    }

    protected function getData(): array
    {
        ['labels' => $labels, 'data' => $data] = $this->resolveChartData();

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.revenue-by-category.revenue'),
                    'data'            => $data,
                    'backgroundColor' => array_slice($this->chartRamp(), 0, max(count($data), 1)),
                    'borderWidth'     => 0,
                    'hoverOffset'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $total = array_sum($this->resolveChartData()['data']);

        return $this->doughnutChartOptions(
            compact_money($total, current_company()?->currency?->name),
            __('sales::filament/widgets/sales-dashboard.revenue-by-category.revenue'),
        );
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
