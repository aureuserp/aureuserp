<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class MonthlySalesChart extends ChartWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected const BAR_COLOR = '#2a78d6';

    protected const INK_MUTED = '#898781';

    protected const GRID = 'rgba(137, 135, 129, 0.25)';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.monthly-sales.heading');
    }

    protected function getData(): array
    {
        $query = $this->saleOrders();

        $bucket = db_dialect()->monthBucket($query->qualifyColumn('date_order'));

        $revenues = $query
            ->selectRaw("{$bucket} as period, SUM(amount_total) as revenue")
            ->groupByRaw($bucket)
            ->pluck('revenue', 'period');

        [$start, $end] = $this->periodRange();

        $labels = [];
        $data = [];
        $cursor = $start->copy()->startOfMonth();

        while ($cursor <= $end) {
            $labels[] = $cursor->translatedFormat('M Y');
            $data[] = round((float) ($revenues[$cursor->format('Y-m')] ?? 0), 2);
            $cursor->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.monthly-sales.revenue'),
                    'data'            => $data,
                    'backgroundColor' => self::BAR_COLOR,
                    'borderRadius'    => 4,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 28,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins'             => ['legend' => ['display' => false]],
            'scales'              => [
                'x' => [
                    'border' => ['display' => false],
                    'grid'   => ['display' => false],
                    'ticks'  => ['color' => self::INK_MUTED],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'border'      => ['display' => false],
                    'grid'        => ['color' => self::GRID],
                    'ticks'       => ['color' => self::INK_MUTED],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
