<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use DateInterval;
use DatePeriod;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class PurchaseTrendWidget extends DashboardChartWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected const SERIES_COLORS = [
        'draft'    => 'gray',
        'sent'     => 'info',
        'purchase' => 'success',
        'done'     => 'warning',
        'canceled' => 'danger',
    ];

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '320px';

    public function getHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.trend.heading');
    }

    protected function getData(): array
    {
        [$start, $end] = $this->periodRange();

        $orders = $this->purchaseOrders()->get(['ordered_at', 'state']);

        $byDay = $orders->groupBy(fn ($order) => Carbon::parse($order->ordered_at)->format('Y-m-d'));

        $labels = [];
        $series = array_fill_keys(array_keys(self::SERIES_COLORS), []);

        foreach (new DatePeriod($start, new DateInterval('P1D'), (clone $end)->addDay()) as $date) {
            $labels[] = $date->format('M d');

            $daily = $byDay[$date->format('Y-m-d')] ?? collect();

            foreach (array_keys(self::SERIES_COLORS) as $state) {
                $series[$state][] = $daily->filter(fn ($order) => $this->stateValue($order->state) === $state)->count();
            }
        }

        return [
            'datasets' => array_map(
                fn (string $state): array => [
                    'label'            => __('purchases::filament/admin/widgets/purchase-dashboard.trend.states.'.$state),
                    'data'             => $series[$state],
                    'borderColor'      => $this->brandColor(self::SERIES_COLORS[$state]),
                    'backgroundColor'  => $this->brandColor(self::SERIES_COLORS[$state], 500, 0.12),
                    'borderWidth'      => 2,
                    'pointRadius'      => 0,
                    'pointHoverRadius' => 4,
                    'tension'          => 0.3,
                ],
                array_keys(self::SERIES_COLORS)
            ),
            'labels' => $labels,
        ];
    }

    protected function stateValue(mixed $state): string
    {
        return $state instanceof \BackedEnum ? (string) $state->value : (string) $state;
    }

    protected function getOptions(): array
    {
        $options = $this->lineChartOptions(12);

        $options['plugins']['legend'] = [
            'display'  => true,
            'position' => 'bottom',
            'labels'   => [
                'color'         => $this->brandColor('gray', 500),
                'boxWidth'      => 10,
                'boxHeight'     => 10,
                'usePointStyle' => true,
                'padding'       => 14,
            ],
        ];

        return $options;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
