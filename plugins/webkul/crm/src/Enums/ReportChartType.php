<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ReportChartType: string implements HasIcon, HasLabel
{
    case BAR = 'bar';

    case LINE = 'line';

    case PIE = 'pie';

    public function getLabel(): string
    {
        return match ($this) {
            self::BAR  => __('crm::enums/report-chart-type.bar'),
            self::LINE => __('crm::enums/report-chart-type.line'),
            self::PIE  => __('crm::enums/report-chart-type.pie'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::BAR  => 'heroicon-o-chart-bar',
            self::LINE => 'heroicon-o-presentation-chart-line',
            self::PIE  => 'heroicon-o-chart-pie',
        };
    }
}
