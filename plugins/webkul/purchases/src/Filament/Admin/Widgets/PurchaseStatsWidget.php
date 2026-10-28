<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class PurchaseStatsWidget extends DashboardStatsWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $confirmed = ['purchase', 'done'];

        return [
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.draft-rfqs'),
                'heroicon-o-document-text',
                'gray',
                $this->countByState(['draft']),
                $this->countByState(['draft'], $previousStart, $previousEnd),
                view: 'draft-rfqs',
            ),
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.sent-rfqs'),
                'heroicon-o-paper-airplane',
                'info',
                $this->countByState(['sent']),
                $this->countByState(['sent'], $previousStart, $previousEnd),
                view: 'sent-rfqs',
            ),
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.confirmed-orders'),
                'heroicon-o-check-circle',
                'success',
                $this->countByState($confirmed),
                $this->countByState($confirmed, $previousStart, $previousEnd),
                view: 'confirmed-orders',
            ),
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.total-purchase-value'),
                'heroicon-o-currency-dollar',
                'primary',
                $this->sumByState($confirmed),
                $this->sumByState($confirmed, $previousStart, $previousEnd),
                money: true,
                view: 'confirmed-orders',
            ),
        ];
    }

    protected function buildStat(string $label, string $icon, string $color, float $current, float $previous, bool $money = false, ?string $view = null): Stat
    {
        $value = $money ? compact_money($current, $this->dashboardCurrency()) : number_format($current);

        $stat = $this->themedStat($label, $icon, $color, $value, $current, $previous, [$previous, $current]);

        if ($view !== null) {
            $stat->url(PurchaseOrderResource::getUrl('index', ['activeTableView' => $view]));
        }

        return $stat;
    }

    protected function countByState(array $states, ?Carbon $start = null, ?Carbon $end = null): float
    {
        return (float) $this->scopedToStates($states, $start, $end)->count();
    }

    protected function sumByState(array $states, ?Carbon $start = null, ?Carbon $end = null): float
    {
        return (float) $this->scopedToStates($states, $start, $end)->sum('total_cc_amount');
    }

    protected function scopedToStates(array $states, ?Carbon $start, ?Carbon $end): Builder
    {
        $query = $this->purchaseOrders($start, $end);

        return $query->whereIn($query->qualifyColumn('state'), $states);
    }
}
