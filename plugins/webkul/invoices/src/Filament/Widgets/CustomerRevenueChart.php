<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class CustomerRevenueChart extends ChartWidget
{
    use HasInvoiceDashboardFilters, HasWidgetShield;

    protected const RAMP = ['#184f95', '#2a78d6', '#5598e7', '#86b6ef'];

    protected const TOP_SLICES = 3;

    protected const INK_MUTED = '#898781';

    protected static ?int $sort = 4;

    protected static bool $isLazy = true;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.customer-revenue.heading');
    }

    protected function getData(): array
    {
        $rows = $this->customerInvoices()
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'accounts_account_moves.partner_id')
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->addSelect('partners_partners.name as name')
            ->selectRaw('SUM(accounts_account_moves.amount_total) as total_billed')
            ->havingRaw('SUM(accounts_account_moves.amount_total) > 0')
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

        return [
            'datasets' => [
                [
                    'label'           => __('invoices::filament/widgets/invoice-dashboard.customer-revenue.total-billed'),
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
