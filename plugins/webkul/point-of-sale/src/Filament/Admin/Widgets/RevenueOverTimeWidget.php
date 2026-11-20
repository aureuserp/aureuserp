<?php

namespace Webkul\PointOfSale\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Filament\Admin\Widgets\Concerns\FiltersDashboardOrders;
use Webkul\PointOfSale\Models\Order;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class RevenueOverTimeWidget extends DashboardChartWidget
{
    use FiltersDashboardOrders, HasWidgetShield;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): string|Htmlable|null
    {
        return __('point-of-sale::filament/admin/widgets/revenue-over-time.heading');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        [$start, $end] = $this->currentOrderRange();

        $query = $this->applyNonDateOrderFilters(Order::query())
            ->whereIn('pos_orders.state', [
                OrderState::PAID->value,
                OrderState::DONE->value,
                OrderState::INVOICED->value,
            ]);

        $trend = Trend::query($query)
            ->dateColumn('ordered_at')
            ->between(start: $start, end: $end)
            ->perDay()
            ->sum('amount_total');

        $data = $trend->map(fn (TrendValue $value): float => round((float) $value->aggregate, 2))->all();

        return [
            'datasets' => [
                $this->gradientLineDataset($data, __('point-of-sale::filament/admin/widgets/revenue-over-time.dataset')),
            ],
            'labels' => $trend->map(fn (TrendValue $value): string => Carbon::parse($value->date)->format('M d'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        $options = $this->lineChartOptions(12);

        $options['plugins']['lineGradient'] = $this->revenueLineGradient();

        return $options;
    }
}
