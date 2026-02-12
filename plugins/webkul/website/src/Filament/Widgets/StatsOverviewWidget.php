<?php

namespace Webkul\Website\Filament\Widget;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected function getHeading(): ?string
    {
        return __('website::filament/widgets/stats-overview.heading');
    }
}
