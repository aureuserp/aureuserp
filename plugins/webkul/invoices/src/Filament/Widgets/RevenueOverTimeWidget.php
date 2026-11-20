<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use DateInterval;
use DatePeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasChartTheme;

class RevenueOverTimeWidget extends ChartWidget
{
    use HasChartTheme, HasInvoiceDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

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
            ->get(['invoice_date', 'amount_total_signed'])
            ->groupBy(fn ($invoice) => Carbon::parse($invoice->invoice_date)->format('Y-m-d'))
            ->map(fn ($group) => round((float) $group->sum('amount_total_signed'), 2));

        $labels = [];
        $data = [];

        foreach (new DatePeriod($start, new DateInterval('P1D'), (clone $end)->addDay()) as $date) {
            $labels[] = $date->format('M d');
            $data[] = $totals[$date->format('Y-m-d')] ?? 0;
        }

        return [
            'datasets' => [
                $this->gradientLineDataset($data),
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction'         => $this->chartInteraction('index'),
            'plugins'             => [
                'entrance' => ['type' => 'progressive-line', 'duration' => 700, 'stagger' => 22],

                'lineGradient' => $this->revenueLineGradient(),

                'legend'  => ['display' => false],
                'tooltip' => $this->chartTooltip(),
            ],
            'scales'              => $this->cartesianScales(12),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
