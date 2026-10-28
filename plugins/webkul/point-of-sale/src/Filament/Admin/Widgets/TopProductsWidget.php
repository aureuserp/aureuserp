<?php

namespace Webkul\PointOfSale\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Filament\Admin\Widgets\Concerns\FiltersDashboardOrders;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class TopProductsWidget extends DashboardChartWidget
{
    use FiltersDashboardOrders, HasWidgetShield;

    protected static ?int $sort = 7;

    protected ?string $maxHeight = '320px';

    public function getHeading(): string|Htmlable|null
    {
        return __('point-of-sale::filament/admin/widgets/top-products.heading');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $lines = OrderLine::query()
            ->select('product_id', DB::raw('SUM(price_subtotal) as revenue'))
            ->whereHas('order', fn (Builder $query) => $this->applyOrderFilters(
                $query->whereIn('state', [
                    OrderState::PAID,
                    OrderState::DONE,
                    OrderState::INVOICED,
                ]),
            ))
            ->when($this->hasProductFilters(), fn (Builder $query) => $this->applyOrderLineProductFilters($query))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->with('product')
            ->get();

        $data = $lines->pluck('revenue')->map(fn ($revenue): float => (float) $revenue)->all();

        $ramp = $this->chartRamp();

        return [
            'datasets' => [
                [
                    'label'           => __('point-of-sale::filament/admin/widgets/top-products.dataset'),
                    'data'            => $data,
                    'backgroundColor' => array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($data)),
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $lines->map(fn (OrderLine $line): string => $line->product?->name ?? '—')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions(10);
    }
}
