<?php

namespace Webkul\Maintenance\Enums;

use Filament\Support\Contracts\HasLabel;

enum MaintenanceRepeatUnit: string implements HasLabel
{
    case DAY = 'day';

    case WEEK = 'week';

    case MONTH = 'month';

    case YEAR = 'year';

    public function getLabel(): string
    {
        return match ($this) {
            self::DAY   => __('maintenance::enums/maintenance-repeat-unit.day'),
            self::WEEK  => __('maintenance::enums/maintenance-repeat-unit.week'),
            self::MONTH => __('maintenance::enums/maintenance-repeat-unit.month'),
            self::YEAR  => __('maintenance::enums/maintenance-repeat-unit.year'),
        };
    }
}
