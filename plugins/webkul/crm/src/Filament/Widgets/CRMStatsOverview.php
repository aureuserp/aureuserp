<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class CRMStatsOverview extends DashboardStatsWidget
{
    use HasCRMDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return __('crm::filament/widgets/crm-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $currency = $this->currencyCode();

        $leads = $this->createdLeads()->count();
        $opportunities = $this->createdOpportunities()->count();
        $wonRevenue = (float) $this->wonOpportunities()->sum('expected_revenue');
        $winRate = $this->winRate($this->wonOpportunities()->count(), $this->lostOpportunities()->count());

        $previousWinRate = $this->winRate(
            $this->wonOpportunities($previousStart, $previousEnd)->count(),
            $this->lostOpportunities($previousStart, $previousEnd)->count(),
        );

        return [
            $this->themedStat(
                __('crm::filament/widgets/crm-dashboard.stats.new-leads'),
                'heroicon-o-user-plus',
                'info',
                number_format($leads),
                $leads,
                $this->createdLeads($previousStart, $previousEnd)->count(),
                $this->trend($this->createdLeads(), 'created_at', 'COUNT', '*'),
            ),
            $this->themedStat(
                __('crm::filament/widgets/crm-dashboard.stats.new-opportunities'),
                'heroicon-o-light-bulb',
                'primary',
                number_format($opportunities),
                $opportunities,
                $this->createdOpportunities($previousStart, $previousEnd)->count(),
                $this->trend($this->createdOpportunities(), 'created_at', 'COUNT', '*'),
            ),
            $this->themedStat(
                __('crm::filament/widgets/crm-dashboard.stats.won-revenue'),
                'heroicon-o-banknotes',
                'success',
                compact_money($wonRevenue, $currency),
                $wonRevenue,
                (float) $this->wonOpportunities($previousStart, $previousEnd)->sum('expected_revenue'),
                $this->trend($this->wonOpportunities(), 'closed_at', 'SUM', 'expected_revenue'),
                money($wonRevenue, $currency),
            ),
            $this->themedStat(
                __('crm::filament/widgets/crm-dashboard.stats.win-rate'),
                'heroicon-o-trophy',
                'warning',
                Number::percentage($winRate, 1),
                $winRate,
                $previousWinRate,
            ),
        ];
    }

    protected function winRate(int $won, int $lost): float
    {
        return ($won + $lost) > 0 ? $won / ($won + $lost) * 100 : 0.0;
    }

    protected function trend(Builder $query, string $dateColumn, string $aggregate, string $column): array
    {
        [$start, $end] = $this->periodRange();

        return Trend::query($query)
            ->dateColumn($dateColumn)
            ->between(start: $start, end: $end)
            ->perDay()
            ->aggregate($column, $aggregate)
            ->map(fn (TrendValue $value): float => round((float) $value->aggregate, 2))
            ->toArray();
    }
}
