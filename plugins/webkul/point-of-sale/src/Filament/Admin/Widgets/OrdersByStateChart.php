<?php

namespace Webkul\PointOfSale\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Filament\Admin\Widgets\Concerns\FiltersDashboardOrders;
use Webkul\PointOfSale\Models\Order;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class OrdersByStateChart extends DashboardChartWidget
{
    use FiltersDashboardOrders, HasWidgetShield;

    protected static ?int $sort = 3;

    public function getHeading(): string|Htmlable|null
    {
        return __('point-of-sale::filament/admin/widgets/orders-by-state.heading');
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = $this->applyOrderFilters(Order::query())
            ->selectRaw('state, COUNT(*) as total')
            ->groupBy('state')
            ->pluck('total', 'state');

        $states = collect(OrderState::cases())
            ->filter(fn (OrderState $state): bool => (int) ($counts[$state->value] ?? 0) > 0);

        return [
            'datasets' => [
                [
                    'label'           => __('point-of-sale::filament/admin/widgets/orders-by-state.dataset'),
                    'data'            => $states->map(fn (OrderState $state): int => (int) $counts[$state->value])->values()->all(),
                    'backgroundColor' => $states->map(fn (OrderState $state): string => $this->brandColor($state->getColor()))->values()->all(),
                    'borderWidth'     => 0,
                    'hoverOffset'     => 0,
                ],
            ],
            'labels' => $states->map(fn (OrderState $state): string => $state->getLabel())->values()->all(),
        ];
    }

    protected function getOptions(): array
    {
        return $this->doughnutChartOptions();
    }
}
