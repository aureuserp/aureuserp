<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Widgets\StatsOverviewWidget\Stat;

trait BuildsDashboardStats
{
    /**
     * @param  list<float>  $series
     */
    protected function themedStat(string $label, string $icon, string $color, string $value, float $current, float $previous, array $series = [], ?string $title = null): Stat
    {
        [$description, $descriptionIcon, $trendColor] = $this->statTrend($current, $previous);

        $attributes = [
            'class' => 'dashboard-stat dashboard-stat--'.$color,
        ];

        if ($title !== null) {
            $attributes['title'] = $title;
        }

        $stat = Stat::make($label, $value)
            ->description($description)
            ->descriptionIcon($descriptionIcon)
            ->descriptionColor($trendColor)
            ->chartColor($color)
            ->icon($icon)
            ->extraAttributes($attributes);

        return $series === [] ? $stat : $stat->chart($series);
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    protected function statTrend(float $current, float $previous): array
    {
        if ($previous == 0.0 && $current == 0.0) {
            return ['0%', 'heroicon-m-minus', 'gray'];
        }

        $percentage = $previous == 0.0 ? 100.0 : round((($current - $previous) / abs($previous)) * 100, 1);

        $label = abs($percentage).'%';

        if ($percentage < 0) {
            return [$label, 'heroicon-m-arrow-trending-down', 'danger'];
        }

        if ($percentage == 0.0) {
            return [$label, 'heroicon-m-minus', 'gray'];
        }

        return [$label, 'heroicon-m-arrow-trending-up', 'success'];
    }
}
