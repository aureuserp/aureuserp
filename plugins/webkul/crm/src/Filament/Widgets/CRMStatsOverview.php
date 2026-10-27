<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;

class CRMStatsOverview extends BaseWidget
{
    use HasCRMDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('crm::filament/widgets/crm-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $leads = $this->createdLeads()->count();
        $opportunities = $this->createdOpportunities()->count();
        $wonRevenue = (float) $this->wonOpportunities()->sum('expected_revenue');
        $winRate = $this->winRate($this->wonOpportunities()->count(), $this->lostOpportunities()->count());

        $previousWinRate = $this->winRate(
            $this->wonOpportunities($previousStart, $previousEnd)->count(),
            $this->lostOpportunities($previousStart, $previousEnd)->count(),
        );

        return [
            $this->makeStat(__('crm::filament/widgets/crm-dashboard.stats.new-leads'), Number::format($leads), $leads, $this->createdLeads($previousStart, $previousEnd)->count(), $this->trend($this->createdLeads(), 'created_at', 'COUNT', '*')),
            $this->makeStat(__('crm::filament/widgets/crm-dashboard.stats.new-opportunities'), Number::format($opportunities), $opportunities, $this->createdOpportunities($previousStart, $previousEnd)->count(), $this->trend($this->createdOpportunities(), 'created_at', 'COUNT', '*')),
            $this->makeStat(__('crm::filament/widgets/crm-dashboard.stats.won-revenue'), Number::currency($wonRevenue, $this->currencyCode()), $wonRevenue, (float) $this->wonOpportunities($previousStart, $previousEnd)->sum('expected_revenue'), $this->trend($this->wonOpportunities(), 'closed_at', 'SUM', 'expected_revenue')),
            $this->makeStat(__('crm::filament/widgets/crm-dashboard.stats.win-rate'), Number::percentage($winRate, 1), $winRate, $previousWinRate, []),
        ];
    }

    protected function winRate(int $won, int $lost): float
    {
        return ($won + $lost) > 0 ? $won / ($won + $lost) * 100 : 0.0;
    }

    protected function makeStat(string $label, string $display, float $current, float $previous, array $chart): Stat
    {
        $change = $previous == 0.0 ? ($current > 0 ? 100.0 : 0.0) : ($current - $previous) / abs($previous) * 100;

        $stat = Stat::make($label, $display)
            ->description(abs(round($change, 1)).'% '.($change >= 0 ? __('crm::filament/widgets/crm-dashboard.stats.increase') : __('crm::filament/widgets/crm-dashboard.stats.decrease')))
            ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($change >= 0 ? 'success' : 'danger');

        return $chart === [] ? $stat : $stat->chart($chart);
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
