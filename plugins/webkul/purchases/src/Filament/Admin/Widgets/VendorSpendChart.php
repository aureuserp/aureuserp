<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;

class VendorSpendChart extends ChartWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected const RAMP = ['#184f95', '#2a78d6', '#5598e7', '#86b6ef'];

    protected const TOP_SLICES = 3;

    protected const INK_MUTED = '#898781';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.vendor-spend.heading');
    }

    protected function getData(): array
    {
        $query = $this->purchaseOrders();

        $rows = $query
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'purchases_orders.partner_id')
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->addSelect('partners_partners.name as name')
            ->selectRaw('SUM(purchases_orders.total_amount) as total_purchased')
            ->havingRaw('SUM(purchases_orders.total_amount) > 0')
            ->orderByDesc('total_purchased')
            ->get();

        $labels = [];
        $data = [];

        foreach ($rows->take(self::TOP_SLICES) as $row) {
            $labels[] = $row->name ?: __('purchases::filament/admin/widgets/purchase-dashboard.unknown');
            $data[] = round((float) $row->total_purchased, 2);
        }

        $other = round((float) $rows->skip(self::TOP_SLICES)->sum('total_purchased'), 2);

        if ($other > 0) {
            $labels[] = __('purchases::filament/admin/widgets/purchase-dashboard.other');
            $data[] = $other;
        }

        return [
            'datasets' => [
                [
                    'label'           => __('purchases::filament/admin/widgets/purchase-dashboard.vendor-spend.total-purchased'),
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
