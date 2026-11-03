<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasChartTheme;

class CustomerRevenueChart extends ChartWidget
{
    use HasChartTheme, HasInvoiceDashboardFilters, HasWidgetShield;

    protected const TOP_SLICES = 3;

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    protected ?string $maxHeight = '300px';

    /**
     * @var array{labels: list<string>, data: list<float>}|null
     */
    protected ?array $resolvedData = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.customer-revenue.heading');
    }

    /**
     * @return array{labels: list<string>, data: list<float>}
     */
    protected function resolveChartData(): array
    {
        if ($this->resolvedData !== null) {
            return $this->resolvedData;
        }

        $rows = $this->customerInvoices()
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'accounts_account_moves.partner_id')
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->addSelect('partners_partners.name as name')
            ->selectRaw('SUM(accounts_account_moves.amount_total_signed) as total_billed')
            ->havingRaw('SUM(accounts_account_moves.amount_total_signed) > 0')
            ->orderByDesc('total_billed')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows->take(self::TOP_SLICES) as $row) {
            $labels[] = $row->name ?: __('invoices::filament/widgets/invoice-dashboard.unknown');
            $data[] = round((float) $row->total_billed, 2);
        }

        $other = round((float) $rows->skip(self::TOP_SLICES)->sum('total_billed'), 2);

        if ($other > 0) {
            $labels[] = __('invoices::filament/widgets/invoice-dashboard.other');
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
                    'label'           => '',
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

        return [
            'maintainAspectRatio' => false,
            'cutout'              => '62%',
            'layout'              => ['padding' => 26],
            'interaction'         => $this->chartInteraction('nearest', true),
            'plugins'             => [
                'entrance'   => ['type' => 'sweep', 'duration' => 900],

                'hoverGrow'  => ['grow' => 20, 'speed' => 0.16],

                'legend'     => [
                    'position' => 'right',
                    'labels'   => [
                        'color'         => $this->brandColor('gray', 500),
                        'boxWidth'      => 12,
                        'boxHeight'     => 12,
                        'usePointStyle' => true,
                        'pointStyle'    => 'rectRounded',
                        'padding'       => 14,
                    ],
                ],
                'tooltip'    => array_merge($this->chartTooltip(), ['percentage' => true]),
                'centerText' => [
                    'text'       => compact_money($total, $this->dashboardCurrency()),
                    'label'      => __('invoices::filament/widgets/invoice-dashboard.customer-revenue.center-label'),
                    'labelColor' => $this->brandColor('gray', 500),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
