<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class MonthlySalesChart extends ChartWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

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
        $bucket = db_dialect()->monthBucket('date_order');

        $revenues = $this->saleOrders()
            ->selectRaw("{$bucket} as period, SUM(amount_total) as revenue")
            ->groupByRaw($bucket)
            ->pluck('revenue', 'period');

        [$start, $end] = $this->periodRange();

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

    protected function getType(): string
    {
        return 'bar';
    }
}
