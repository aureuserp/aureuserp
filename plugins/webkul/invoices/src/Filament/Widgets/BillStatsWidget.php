<?php

namespace Webkul\Invoice\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;

class BillStatsWidget extends BaseWidget
{
    use HasInvoiceDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('invoices::filament/widgets/invoice-dashboard.bill-stats.heading');
    }

    protected function getStats(): array
    {
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        return [
            $this->makeStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.total'),
                'heroicon-o-currency-dollar',
                (float) $this->scope()->sum('amount_total'),
                (float) $this->scope($previousStart, $previousEnd)->sum('amount_total'),
                money: true,
            ),
            $this->makeStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.count'),
                'heroicon-o-document-text',
                (float) $this->scope()->count(),
                (float) $this->scope($previousStart, $previousEnd)->count(),
            ),
            $this->makeStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.paid'),
                'heroicon-o-check-circle',
                (float) $this->byPaymentState('paid'),
                (float) $this->byPaymentState('paid', $previousStart, $previousEnd),
            ),
            $this->makeStat(
                __('invoices::filament/widgets/invoice-dashboard.bill-stats.unpaid'),
                'heroicon-o-exclamation-circle',
                (float) $this->byPaymentState('not_paid'),
                (float) $this->byPaymentState('not_paid', $previousStart, $previousEnd),
            ),
        ];
    }

    protected function scope(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->vendorBills($start, $end);
    }

    protected function byPaymentState(string $state, ?Carbon $start = null, ?Carbon $end = null): int
    {
        $query = $this->scope($start, $end);

        return $query->where($query->qualifyColumn('payment_state'), $state)->count();
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
            return [__('invoices::filament/widgets/invoice-dashboard.no-change'), 'heroicon-m-minus', 'gray'];
        }

        $percentage = $previous == 0.0 ? 100.0 : round((($current - $previous) / abs($previous)) * 100, 1);

        if ($percentage < 0) {
            return [
                __('invoices::filament/widgets/invoice-dashboard.decrease', ['percent' => abs($percentage)]),
                'heroicon-m-arrow-trending-down',
                'danger',
            ];
        }

        if ($percentage == 0.0) {
            return [__('invoices::filament/widgets/invoice-dashboard.no-change'), 'heroicon-m-minus', 'gray'];
        }

        return [
            __('invoices::filament/widgets/invoice-dashboard.increase', ['percent' => $percentage]),
            'heroicon-m-arrow-trending-up',
            'success',
        ];
    }
}
