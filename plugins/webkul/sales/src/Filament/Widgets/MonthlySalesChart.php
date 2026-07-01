<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class MonthlySalesChart extends ChartWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.monthly-sales.heading');
    }

    protected function getData(): array
    {
        $revenues = $this->saleOrders()
            ->selectRaw("DATE_FORMAT(date_order, '%Y-%m') as period, SUM(amount_total) as revenue")
            ->groupBy('period')
            ->pluck('revenue', 'period');

        [$start, $end] = $this->getPeriodRange();

        $labels = [];
        $data = [];
        $cursor = $start->copy()->startOfMonth();

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->translatedFormat('M Y');
            $data[] = round((float) ($revenues[$key] ?? 0), 2);
            $cursor->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.monthly-sales.revenue'),
                    'data'            => $data,
                    'backgroundColor' => '#22c55e',
                    'borderColor'     => '#16a34a',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getPeriodRange(): array
    {
        $filters = $this->pageFilters ?? [];

        $start = ! empty($filters['startDate']) ? Carbon::parse($filters['startDate']) : now()->subMonths(11);

        $end = ! empty($filters['endDate']) ? Carbon::parse($filters['endDate']) : now();

        return [$start, $end];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
