<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasChartTheme;

class InvoiceStatusChart extends ChartWidget
{
    use HasChartTheme, HasInvoiceDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.invoice-status.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.invoice-status.description');
    }

    protected function getData(): array
    {
        $query = $this->customerInvoices();

        $column = $query->qualifyColumn('payment_state');

        $amount = $query->qualifyColumn('amount_total_signed');

        $totals = $query
            ->groupBy($column)
            ->selectRaw("{$column} as state")
            ->selectRaw("SUM({$amount}) as total")
            ->pluck('total', 'state');

        $labels = [];
        $data = [];
        $colors = [];

        foreach (PaymentState::cases() as $state) {
            $value = round(abs((float) ($totals[$state->value] ?? 0)), 2);

            if ($value <= 0) {
                continue;
            }

            $labels[] = $state->getLabel();

            $data[] = $value;

            $colors[] = $this->brandColor($state->getColor() ?? 'gray');
        }

        return [
            'datasets' => [
                [
                    'label'           => '',
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 40,
                ],
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
