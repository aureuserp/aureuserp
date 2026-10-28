<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasChartTheme;

class SalespersonPerformanceChart extends ChartWidget
{
    use HasChartTheme, HasInvoiceDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 6;

    protected static bool $isLazy = true;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.salespersons.heading');
    }

    protected function getData(): array
    {
        $rows = $this->customerInvoices()
            ->whereNotNull('accounts_account_moves.invoice_user_id')
            ->join('users', 'users.id', '=', 'accounts_account_moves.invoice_user_id')
            ->groupBy('users.id', 'users.name')
            ->addSelect('users.name as name')
            ->selectRaw('SUM(accounts_account_moves.amount_total_signed) as total_billed')
            ->havingRaw('SUM(accounts_account_moves.amount_total_signed) > 0')
            ->orderByDesc('total_billed')
            ->limit(6)
            ->get();

        $values = $rows->map(fn ($row): float => round((float) $row->total_billed, 2))->all();

        $ramp = $this->chartRamp();

        $colors = array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($values));

        return [
            'datasets' => [
                [
                    'label'           => '',
                    'data'            => $values,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $rows
                ->map(fn ($row): string => $row->name ?: __('invoices::filament/widgets/invoice-dashboard.unknown'))
                ->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'interaction'         => $this->chartInteraction('index'),
            'plugins'             => [
                'entrance' => ['type' => 'rising-bars', 'duration' => 700, 'stagger' => 90],

                'legend'  => ['display' => false],
                'tooltip' => $this->chartTooltip(),
            ],
            'scales'              => $this->cartesianScales(6, false),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
