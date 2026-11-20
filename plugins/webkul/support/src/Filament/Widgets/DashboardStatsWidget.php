<?php

namespace Webkul\Support\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Webkul\Support\Filament\Concerns\BuildsDashboardStats;

abstract class DashboardStatsWidget extends StatsOverviewWidget
{
    use BuildsDashboardStats;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';
}
