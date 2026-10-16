<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class RevenueByCategoryChart extends ChartWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected const RAMP = ['#184f95', '#2a78d6', '#5598e7', '#86b6ef'];

    protected const TOP_SLICES = 3;

    protected const INK_MUTED = '#898781';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.revenue-by-category.heading');
    }

    protected function getData(): array
    {
        $rows = $this->confirmedLines()
            ->leftJoin('products_products', 'products_products.id', '=', 'sales_order_lines.product_id')
            ->leftJoin('products_categories', 'products_categories.id', '=', 'products_products.category_id')
            ->groupBy('products_categories.id', 'products_categories.name')
            ->addSelect('products_categories.name as name')
            ->selectRaw('SUM(sales_order_lines.price_total) as revenue')
            ->havingRaw('SUM(sales_order_lines.price_total) > 0')
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

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.revenue-by-category.revenue'),
                    'data'            => $data,
                    'backgroundColor' => array_slice(self::RAMP, 0, max(count($data), 1)),
                    'borderWidth'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'cutout'              => '58%',
            'plugins'             => [
                'legend' => [
                    'position' => 'right',
                    'labels'   => [
                        'color'         => self::INK_MUTED,
                        'boxWidth'      => 10,
                        'boxHeight'     => 10,
                        'usePointStyle' => true,
                        'padding'       => 14,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
