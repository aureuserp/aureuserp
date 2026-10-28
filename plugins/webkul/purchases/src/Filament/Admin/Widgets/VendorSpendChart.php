<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class VendorSpendChart extends DashboardChartWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected const TOP_SLICES = 3;

    protected static ?int $sort = 3;

    protected ?array $resolvedData = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.vendor-spend.heading');
    }

    protected function getData(): array
    {
        return $this->resolvedData ??= $this->resolveData();
    }

    protected function resolveData(): array
    {
        $query = $this->purchaseOrders();

        $rows = $query
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'purchases_orders.partner_id')
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->addSelect('partners_partners.name as name')
            ->selectRaw('SUM(purchases_orders.total_cc_amount) as total_purchased')
            ->havingRaw('SUM(purchases_orders.total_cc_amount) > 0')
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

        $ramp = $this->chartRamp();

        return [
            'datasets' => [
                [
                    'label'           => __('purchases::filament/admin/widgets/purchase-dashboard.vendor-spend.total-purchased'),
                    'data'            => $data,
                    'backgroundColor' => array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($data)),
                    'borderWidth'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->doughnutChartOptions(
            compact_money((float) array_sum($this->getData()['datasets'][0]['data']), $this->dashboardCurrency()),
            __('purchases::filament/admin/widgets/purchase-dashboard.vendor-spend.total-purchased'),
        );
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
