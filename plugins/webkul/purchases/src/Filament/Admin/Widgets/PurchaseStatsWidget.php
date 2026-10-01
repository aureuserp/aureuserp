<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;

class PurchaseStatsWidget extends BaseWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $confirmed = ['purchase', 'done'];

        return [
            $this->makeStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.draft-rfqs'),
                'heroicon-o-document-text',
                $this->countByState(['draft']),
                $this->countByState(['draft'], $previousStart, $previousEnd),
            ),
            $this->makeStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.sent-rfqs'),
                'heroicon-o-paper-airplane',
                $this->countByState(['sent']),
                $this->countByState(['sent'], $previousStart, $previousEnd),
            ),
            $this->makeStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.confirmed-orders'),
                'heroicon-o-check-circle',
                $this->countByState($confirmed),
                $this->countByState($confirmed, $previousStart, $previousEnd),
            ),
            $this->makeStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.total-purchase-value'),
                'heroicon-o-currency-dollar',
                $this->sumByState($confirmed),
                $this->sumByState($confirmed, $previousStart, $previousEnd),
                money: true,
            ),
        ];
    }

    protected function countByState(array $states, ?Carbon $start = null, ?Carbon $end = null): float
    {
        return (float) $this->scopedToStates($states, $start, $end)->count();
    }

    protected function sumByState(array $states, ?Carbon $start = null, ?Carbon $end = null): float
    {
        return (float) $this->scopedToStates($states, $start, $end)->sum('total_amount');
    }

    protected function scopedToStates(array $states, ?Carbon $start, ?Carbon $end): Builder
    {
        $query = $this->purchaseOrders($start, $end);

        return $query->whereIn($query->qualifyColumn('state'), $states);
    }

    protected function makeStat(string $label, string $icon, float $current, float $previous, bool $money = false): Stat
    {
        [$description, $descriptionIcon, $color] = $this->trend($current, $previous);

        return Stat::make($label, $money ? money($current, $this->dashboardCurrency()) : number_format($current))
            ->description($description)
            ->descriptionIcon($descriptionIcon)
            ->color($color)
            ->icon($icon)
            ->chart([$previous, $current]);
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    protected function trend(float $current, float $previous): array
    {
        if ($previous == 0.0 && $current == 0.0) {
            return [
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.no-change'),
                'heroicon-m-minus',
                'gray',
            ];
        }

        $percentage = $previous == 0.0 ? 100.0 : round((($current - $previous) / abs($previous)) * 100, 1);

        if ($percentage < 0) {
            return [
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.decrease', ['percent' => abs($percentage)]),
                'heroicon-m-arrow-trending-down',
                'danger',
            ];
        }

        if ($percentage == 0.0) {
            return [
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.no-change'),
                'heroicon-m-minus',
                'gray',
            ];
        }

        return [
            __('purchases::filament/admin/widgets/purchase-dashboard.stats.increase', ['percent' => $percentage]),
            'heroicon-m-arrow-trending-up',
            'success',
        ];
    }
}
