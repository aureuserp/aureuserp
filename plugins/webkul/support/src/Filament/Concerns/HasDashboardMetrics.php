<?php

namespace Webkul\Support\Filament\Concerns;

use Closure;
use DateInterval;
use DatePeriod;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait HasDashboardMetrics
{
    use BuildsDashboardStats;
    use InteractsWithPageFilters;

    protected function dashboardDateColumn(): string
    {
        return 'created_at';
    }

    protected function dashboardValueColumn(): string
    {
        return 'amount_total';
    }

    protected function buildStat(string $label, string $icon, string $color, Closure $queryFactory, bool $money = false, bool $abs = false): Stat
    {
        [$start, $end] = $this->periodRange();

        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $current = $this->aggregate($queryFactory($start, $end), $money);

        $previous = $this->aggregate($queryFactory($previousStart, $previousEnd), $money);

        $series = $this->dailyBuckets($queryFactory($start, $end), $money);

        if ($abs) {
            $current = abs($current);

            $previous = abs($previous);

            $series = array_map('abs', $series);
        }

        $value = $money ? compact_money($current, $this->dashboardCurrency()) : number_format($current);

        $title = $money ? money($current, $this->dashboardCurrency()) : null;

        return $this->themedStat($label, $icon, $color, $value, $current, $previous, $series, $title);
    }

    protected function aggregate(Builder $query, bool $money): float
    {
        return $money
            ? (float) $query->sum($query->qualifyColumn($this->dashboardValueColumn()))
            : (float) $query->count();
    }

    /**
     * @return list<float>
     */
    protected function dailyBuckets(Builder $query, bool $money): array
    {
        [$start, $end] = $this->periodRange();

        $dateColumn = $this->dashboardDateColumn();

        $valueColumn = $this->dashboardValueColumn();

        $byDay = $query
            ->get([$query->qualifyColumn($dateColumn), $query->qualifyColumn($valueColumn)])
            ->groupBy(fn ($record): string => Carbon::parse($record->{$dateColumn})->format('Y-m-d'));

        $series = [];

        foreach (new DatePeriod($start, new DateInterval('P1D'), (clone $end)->addDay()) as $date) {
            $group = $byDay->get($date->format('Y-m-d'));

            $series[] = $group
                ? ($money ? round((float) $group->sum($valueColumn), 2) : (float) $group->count())
                : 0.0;
        }

        return $series;
    }

    /**
     * @return array<string, mixed>
     */
    protected function dashboardFilters(): array
    {
        return $this->pageFilters ?? [];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function periodRange(): array
    {
        $filters = $this->dashboardFilters();

        $start = ! empty($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : now()->subMonth()->startOfDay();

        $end = ! empty($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : now()->endOfDay();

        return [$start, $end];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function previousPeriodRange(): array
    {
        [$start, $end] = $this->periodRange();

        $length = $start->diffInDays($end) + 1;

        $previousEnd = (clone $start)->subDay()->endOfDay();

        return [(clone $previousEnd)->subDays($length - 1)->startOfDay(), $previousEnd];
    }

    protected function dashboardCurrency(): ?string
    {
        return current_company()?->currency?->name;
    }
}
