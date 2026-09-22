<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use DateInterval;
use DatePeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;

class PurchaseTrendWidget extends ChartWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected const SERIES = [
        'draft'    => '#2a78d6',
        'sent'     => '#eb6834',
        'purchase' => '#1baf7a',
        'done'     => '#eda100',
        'canceled' => '#e87ba4',
    ];

    protected const INK_MUTED = '#898781';

    protected const GRID = 'rgba(137, 135, 129, 0.25)';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

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
        $series = array_fill_keys(array_keys(self::SERIES), []);

        foreach (new DatePeriod($start, new DateInterval('P1D'), (clone $end)->addDay()) as $date) {
            $labels[] = $date->format('M d');

            $daily = $byDay[$date->format('Y-m-d')] ?? collect();

            foreach (array_keys(self::SERIES) as $state) {
                $series[$state][] = $daily->filter(fn ($order) => $this->stateValue($order->state) === $state)->count();
            }
        }

        return [
            'datasets' => array_map(
                fn (string $state): array => [
                    'label'            => __('purchases::filament/admin/widgets/purchase-dashboard.trend.states.'.$state),
                    'data'             => $series[$state],
                    'borderColor'      => self::SERIES[$state],
                    'backgroundColor'  => self::SERIES[$state],
                    'borderWidth'      => 2,
                    'pointRadius'      => 0,
                    'pointHoverRadius' => 4,
                    'tension'          => 0.3,
                ],
                array_keys(self::SERIES)
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
        return [
            'maintainAspectRatio' => false,
            'interaction'         => ['mode' => 'index', 'intersect' => false],
            'plugins'             => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => [
                        'color'         => self::INK_MUTED,
                        'boxWidth'      => 10,
                        'boxHeight'     => 10,
                        'usePointStyle' => true,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'border' => ['display' => false],
                    'grid'   => ['display' => false],
                    'ticks'  => ['color' => self::INK_MUTED, 'maxTicksLimit' => 12],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'border'      => ['display' => false],
                    'grid'        => ['color' => self::GRID],
                    'ticks'       => ['color' => self::INK_MUTED, 'precision' => 0],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
