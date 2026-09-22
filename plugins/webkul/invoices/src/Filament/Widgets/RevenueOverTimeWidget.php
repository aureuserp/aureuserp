<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use DateInterval;
use DatePeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class RevenueOverTimeWidget extends ChartWidget
{
    use HasInvoiceDashboardFilters, HasWidgetShield;

    protected const BAR_COLOR = '#2a78d6';

    protected const INK_MUTED = '#898781';

    protected const GRID = 'rgba(137, 135, 129, 0.25)';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.revenue-over-time.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.revenue-over-time.description');
    }

    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();

        $query = $this->customerInvoices();

        $totals = $query
            ->where($query->qualifyColumn('payment_state'), 'paid')
            ->get(['invoice_date', 'amount_total'])
            ->groupBy(fn ($invoice) => Carbon::parse($invoice->invoice_date)->format('Y-m-d'))
            ->map(fn ($group) => round((float) $group->sum('amount_total'), 2));

        $labels = [];
        $data = [];

        foreach (new DatePeriod($start, new DateInterval('P1D'), (clone $end)->addDay()) as $date) {
            $labels[] = $date->format('M d');
            $data[] = $totals[$date->format('Y-m-d')] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => __('invoices::filament/widgets/invoice-dashboard.revenue-over-time.revenue'),
                    'data'            => $data,
                    'backgroundColor' => self::BAR_COLOR,
                    'borderRadius'    => 4,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 24,
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
                    'ticks'  => ['color' => self::INK_MUTED, 'maxTicksLimit' => 12],
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
